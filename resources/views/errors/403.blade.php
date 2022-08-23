

    <div class="error-page">
        <h2 class="headline text-yellow"> 403</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! Página no encontrada.</h3>
            <p>{{$exception->getMessage()}}</p>
            <p>
                Página no Encontrada
                Mientras tanto <a href='{{ url('/') }}'>Regresar al home </a> 
            </p>
            
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
