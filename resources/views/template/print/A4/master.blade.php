<!DOCTYPE html>
<html lang="en" moznomarginboxes mozdisallowselectionprint>

<head>

  @include('template.print.A4.head')
  
  @yield('headmeta')
  
</head>

<body>
  @yield('content')

  @include('template.print.A4.plugin')
    
  @yield('footjs')

</body>

</html>