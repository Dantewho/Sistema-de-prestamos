@include("loyauts.main")
@section("title", "Usuarios")
@section("contenido")
    <h1>Usuarios</h1>
    <p>Lista de usuarios</p>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Tipo usuario</th>
                    <th>solicitudes</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach (usuarios as item)
                    <tr>
                        <td>{{ item.id }}</td>
                        <td>{{ item.usuario }}</td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.tipo_usuario }}</td>
                        <td>{{ item.solicitudes_count }}</td>
                        <td>
                            <a href="" class="btn btn-primary">Editar</a>
                            <form action="" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    
                @endforeach
            </tbody>
        </table>
    </div>
@endsection