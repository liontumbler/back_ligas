<footer class="py-3 mt-4 bg-dark position-relative bottom-0">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="{{ asset('/login') }}" class="nav-link px-2 text-blanco">Login Trabajador</a></li>
        <li class="nav-item"><a href="#" class="nav-link px-2 text-blanco">PQRs</a></li>
    </ul>
    <p class="text-center text-blanco mb-0">Versión {{ env('APP_VERSION') }}</p>
    <p class="text-center text-blanco mb-0"><strong>© {{ now()->year }} Edwin Velasquez Jimenez,</strong> AdminLig</p>
</footer>
