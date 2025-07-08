<!DOCTYPE html>
<html lang="en">

<head>

  @include('template.main.head')
  @yield('headmeta')
  
</head>

<body>
  <!-- Container Fluid-->
  <div class="container-login100" style="background-image: url('{{ request()->routeIs('reset.password*') || request()->routeIs('psb.pendaftaran*') ? '../' : '' }}img/login/pawel-czerwinski-YWIOwHvRBvU-unsplash.jpg')">
  @yield('content')
  </div>
  <!-- Container Fluid-->
  
  @include('template.login.plugin')
    
  @yield('footjs')
</body>

</html>