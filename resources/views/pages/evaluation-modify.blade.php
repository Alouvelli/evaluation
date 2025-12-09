<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'teacher_evaluation') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{asset('plugins/jqvmap/jqvmap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.min.css')}}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-white" style="background-color: #041f4e">
        <div class="container">
            <span class="navbar-brand">
                <img src="{{asset('dist/img/logo_isi.jpg')}}" width="7%" class="img-circle elevation-4" style="opacity: .8">
                <span class="brand-text font-light text-white ml-3">Teacher_Evaluation</span>
            </span>
            <!-- Right navbar links -->
        </div>
    </nav>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @if (session('status'))
            <div class="alert text-white text-center font-weight-bold mt-3" style="background-color: rgba(255,26,35,0.45)" role="alert">
                {{ session('status') }}
            </div>
    @endif
    <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"> Evaluation des enseignements <small class="ml-3 font-weight-bold" style="color: #041f4e">{{--/  {{$profs[0]->libelle}}--}}</small></h1>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <form action="{{route('new_evaluation')}}" method="post">
                @csrf
                <div class="row text-center mb-3 h5">
                    <div class="col-md-10 text-bold ">
                        <marquee scrollamount="8" behavior="5" direction="">Vos réponses sont soumises dans l'anonymat !</marquee>
                    </div>
                </div>
                <input type="number" hidden value="{{$id_etudiant}}" name="id_etudiant">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <!-- ./card-header -->
                            <div class="card-body p-0">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                    @foreach( $profs as $p)
                                        <input type="number" hidden value="{{$profs[0]->id}}" name="id_classe">
                                        <input type="text"  hidden value="{{$p->libelle}}" name="libelle">
                                        <input type="number" hidden value="{{$p->id_professeur}}" name="id_prof{{$p->id_professeur}}">
                                        <tr data-widget="expandable-table" aria-expanded="false">
                                            <td colspan="2" class="text-white h5" style="background-color: #041f4e">
                                                <i class="fas fa-caret-right fa-fw"></i>
                                                {{$p->full_name }} <span class="ml-2 font-italic"> / {{$p->libelle_cours }}</span>
                                            </td>
                                        </tr>
                                        <tr class="expandable-body">
                                            <td>
                                                <div class="p-0">
                                                    <table class="table text-center table-hover table-striped">
                                                        <thead>
                                                        <tr class="text-center text-success h5">
                                                            <th width="70%" style="color: #041f4e">Question</th>
                                                            <th width="30%" style="color: #041f4e">
                                                                <div class="row">
                                                                    <div class="col-md-3">25% (Peu Satisfait)</div>
                                                                    <div class="col-md-3">50% (Satisfait)</div>
                                                                    <div class="col-md-3">75%</div>
                                                                    <div class="col-md-3">100%</div>
                                                                </div>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($questions as $q)
                                                            <tr>
                                                                <td  style="color: #041f4e"><span class="font-weight-bold">Q{{$q->idQ}} </span>: {{$q->libelle}}</td>
                                                                <td>
                                                                    <div class="form-group clearfix row">
                                                                        <div class="icheck-primary d-inline col-md-3">
                                                                            <input type="radio" value="25" id="radioPrimary1_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}" name="{{$p->id_professeur.'_'.$q->idQ.'_'.$p->id_cours}}" required>
                                                                            <label for="radioPrimary1_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}">
                                                                            </label>
                                                                        </div>
                                                                        <div class="icheck-primary d-inline col-md-3">
                                                                            <input  type="radio" value="50" id="radioPrimary2_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}" name="{{$p->id_professeur.'_'.$q->idQ.'_'.$p->id_cours}}" required>
                                                                            <label for="radioPrimary2_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}">
                                                                            </label>
                                                                        </div>
                                                                        <div class="icheck-primary d-inline col-md-3">
                                                                            <input type="radio" value="75" id="radioPrimary3_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}" name="{{$p->id_professeur.'_'.$q->idQ.'_'.$p->id_cours}}" required>
                                                                            <label for="radioPrimary3_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}">
                                                                            </label>
                                                                        </div>
                                                                        <div class="icheck-primary d-inline col-md-3">
                                                                            <input type="radio" value="100" id="radioPrimary4_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}" name="{{$p->id_professeur.'_'.$q->idQ.'_'.$p->id_cours}}" required>
                                                                            <label for="radioPrimary4_{{$q->idQ}}_{{$p->id_professeur}}_{{$p->id_cours}}">
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                                                    </div>
                                                                    <textarea name="com_{{$p->id_professeur}}_{{$p->id_cours}}" id="" cols="40" rows="2" placeholder="Veuillez saisir votre commentaire ici..."></textarea>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-outline-secondary btn-lg mt-2 mb-5">Envoyer <i class="fa fa-paper-plane ml-1" aria-hidden="true"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
    <!-- /.content -->
</div>
</div>
<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- bs-custom-file-input value="" -->
<script src="{{asset('plugins/bs-custom-file-input value=""/bs-custom-file-input value="".min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{asset('plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('dist/js/demo.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('dist/js/pages/dashboard.js')}}"></script>
<!-- Page specific script -->
<script>
    $(function () {
        bsCustomFileinput.value="".init();
    });
</script>
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "excel"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
</body>
</html>

