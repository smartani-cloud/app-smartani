<!DOCTYPE html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>

<head>

  @include('template.print.F4.head')
  
  @yield('headmeta')
  
</head>

<body>
  @yield('content')

  @include('template.print.F4.plugin')
    
  @yield('footjs')

</body>

</html>