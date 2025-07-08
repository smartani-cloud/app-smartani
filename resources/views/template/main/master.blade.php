<!DOCTYPE html>
<html lang="en">

<head>

  @include('template.main.head')
  
  @yield('headmeta')
  
</head>

<body id="page-top" cz-shortcut-listen="true">
  <div id="wrapper">
	<!-- Sidebar -->
    @yield('sidebar')
	
	<!-- Sidebar -->
	
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
		<!-- TopBar -->
        @include('template.main.topbar')
		
		<!-- TopBar -->
		
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          @yield('content')
		  
          <!-- Modal Logout -->
          <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelLogout"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabelLogout">Oh, Tidak!</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Apa Anda yakin ingin keluar?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-brand-green" data-dismiss="modal">Kembali</button>
                  <a href="/logout" class="btn btn-brand-green">Keluar</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!---Container Fluid-->
      </div>
	  
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <script> document.write(new Date().getFullYear()); </script> {{ config('app.name') }}. All Rights Reserved.</b>
            </span>
          </div>
        </div>
      </footer>
      <!-- Footer -->
	  
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  @include('template.main.plugin')
    
  @yield('footjs')
</body>

</html>