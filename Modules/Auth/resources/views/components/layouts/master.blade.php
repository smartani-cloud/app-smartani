<!DOCTYPE html>
<html lang="en">

<head>

  @include('auth::components.layouts.head')
  @yield('headmeta')
  
</head>

<body>
  <!-- Container Fluid-->
  <div class="container-login100" style="background-image: url('{{ request()->routeIs('reset.password*') || request()->routeIs('psb.pendaftaran*') ? '../' : '' }}modules/Auth/img/katrien-van-crombrugghe-mqJp2Bv6bkM-unsplash.jpg')">
  @yield('content')
  </div>
  <!-- Container Fluid-->
  
  @include('auth::components.layouts.plugin')
    
  @yield('footjs')
</body>

</html>
