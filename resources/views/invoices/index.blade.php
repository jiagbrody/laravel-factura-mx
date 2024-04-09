@extends('factura-mx::layouts.app')

@section('title', 'Pagina de facturas')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <h1>Mostrar todas las facturas</h1>

    <div class="flex gap-8">
        <div>1</div>
        <div>2</div>
        <div>3</div>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Fecha</th>
            <th scope="col">Tipo</th>
            <th scope="col">Empresa<br>a facturar</th>
            <th scope="col">Estatus</th>
            <th scope="col" class="list_actions"></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($invoices as $invoice)
            <tr>
                <th scope="row">{{ $invoice->id }}</th>
                <td>{{ $invoice->created_at }}</td>
                <td>{{ $invoice->invoiceType->name }}</td>
                <td>{{ $invoice->invoiceCompany->name }}</td>
                <td>{{ $invoice->invoiceStatus->name }}</td>
                <td>
                    <div class="d-flex actions">
                        <a href="{{ route('laravel-factura-mx.invoices.show', $invoice->id ) }}"
                           class="btn btn-sm btn-primary">
                            Mostrar
                        </a>
                        <button type="button" class="btn btn-sm btn-danger">Cancelar</button>
                    </div>
                </td>
            </tr>
        @empty
            <p class="list-group-item">No invoices yet</p>
        @endforelse
        </tbody>
    </table>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Launch demo modal
    </button>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const myModal = new bootstrap.Modal(document.getElementById("exampleModal"), {});
        document.onreadystatechange = function () {
            myModal.show();
        };
    </script>
@endsection
