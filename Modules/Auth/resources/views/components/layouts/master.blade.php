<!DOCTYPE html>
<html lang="en">

<head>

  @include('auth::components.layouts.head')
  @yield('headmeta')
  
</head>

<body>
  <!-- Container Fluid-->
  <div class="container-login100" style="background-image: url('{{ request()->routeIs('reset.password*') || request()->routeIs('psb.pendaftaran*') ? '../' : '' }}modules/Auth/img/login/PHOTO-2025-06-22-17-03-50.jpg')">
  @yield('content')
  </div>
  <!-- Container Fluid-->
  
  @include('auth::components.layouts.plugin')
    
  @yield('footjs')
</body>

</html>
