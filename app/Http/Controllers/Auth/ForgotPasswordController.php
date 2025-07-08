<?php 
  
namespace App\Http\Controllers\Auth; 
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 

use DB; 
use Carbon\Carbon; 
use App\Models\LoginUser; 

use Mail; 
use Hash;

use Illuminate\Support\Str;
  
class ForgotPasswordController extends Controller
{
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showForgetPasswordForm(Request $request)
      {
        if(isset($request->sent) && $request->sent == "success"){
          return view('auth.reset_password_sent');
        }
        else{
          return view('auth.forget_password');
        }
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitForgetPasswordForm(Request $request)
      {
          $request->validate([
            'email' => 'required|email',
          ]);
          
          if(LoginUser::where('username',$request->email)->whereHas('role',function($q){$q->where('name','ortu');})->aktif()->count() < 1){
              return back()->with('danger', 'Mohon maaf, alamat email Anda tidak terdaftar');
          }
  
          $token = Str::random(64);
  
          DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);
  
          Mail::send('email.forget_password', ['token' => $token], function($message) use($request){
              $message->to($request->email);
              $message->subject('[SISTA] Konfirmasi Reset Sandi');
          });
  
          //return back()->with('message', 'Kami sudah mengirimkan tautan untuk mereset sandi ke email Anda!');
          return redirect()->route('forget.password.get',['sent'=>'success']);
      }

      /**
       * Write code on Method
       *
       * @return response()
       */
      public function showResetPasswordForm($token = null) {
        if($token){
          return view('auth.forget_password_link', ['token' => $token]);
        }
        else{
          return redirect()->route('psb.index',['view' => 'login']);
        }
      }
  
      /**
       * Write code on Method
       *
       * @return response()
       */
      public function submitResetPasswordForm(Request $request)
      {
          $messages = [
            'email.required' => 'Mohon masukkan alamat email Anda',
            'email.email' => 'Mohon periksa kembali alamat email Anda',
            'password.required' => 'Mohon masukkan sandi baru Anda',
            'password.confirmed' => 'Sandi baru Anda harus dikonfirmasi',
            'password.min' => 'Sandi minimal terdiri dari 8 karakter',
            'password_confirmation.required' => 'Mohon konfirmasi sandi baru Anda',
          ];

          $this->validate($request, [
              'email' => 'required|email',
              'password' => 'required|string|min:8|confirmed',
              'password_confirmation' => 'required'
          ], $messages);

          if(LoginUser::where('username',$request->email)->whereHas('role',function($q){$q->where('name','ortu');})->aktif()->count() < 1){
              return back()->with('danger', 'Mohon maaf, alamat email Anda tidak terdaftar');
          }
          
        //   $updatePassword = DB::table('password_resets')->where([
        //     'email' => $request->email, 
        //     'token' => $request->token
        //   ])->orderBy('created_at','DESC')->first();
          
          $lastToken = DB::table('password_resets')->where(['email' => $request->email])->orderBy('created_at','DESC')->first();
  
          if(!$lastToken || ($lastToken && (($lastToken->token != $request->token) || (strtotime(date("Y-m-d H:i:s")) <= (strtotime($lastToken->created_at)))))){
              return back()->withInput()->with('danger', 'Token tidak valid!');
          }
          elseif(strtotime(date("Y-m-d H:i:s")) > (strtotime("+2 hours",strtotime($lastToken->created_at)))){
              return back()->withInput()->with('danger', 'Token sudah kadaluarsa!');
          }
  
          $user = LoginUser::where('username', $request->email)->aktif()->latest()->update(['password' => Hash::make($request->password)]);
 
          DB::table('password_resets')->where(['email'=> $request->email])->delete();
  
          return redirect()->route('psb.index',['view' => 'login'])->with('success', 'Kata sandi Anda berhasil diubah!');
      }
}