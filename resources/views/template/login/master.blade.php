<<<<<<< HEAD
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

=======
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

>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</html>