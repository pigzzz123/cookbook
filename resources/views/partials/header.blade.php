<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top navbar-header">
    <div class="container">
        <!-- Branding Image -->
        <a class="navbar-brand " href="{{ url('/') }}">
            {{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
              <li class="nav-item"><a class="nav-link" href="#">菜谱</a></li>
              <li class="nav-item"><a class="nav-link" href="#">分类</a></li>
              <li class="nav-item"><a class="nav-link" href="#">食材</a></li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav navbar-right">
                <!-- Authentication Links -->
                <li class="nav-item"><a class="nav-link" href="#">登录</a></li>
                <li class="nav-item"><a class="nav-link" href="#">注册</a></li>
            </ul>
        </div>
    </div>
</nav>
<section class="jumbotron jumbotron-fluid">
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center">
      <div class="col-8">
        <form action="#" method="get">
          <div class="input-group input-group-lg">
            <input type="text" class="form-control" name="keywords" placeholder="搜索 菜谱/分类/食材">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button">搜索</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
