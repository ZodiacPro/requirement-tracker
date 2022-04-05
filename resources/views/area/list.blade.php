@extends('layouts.app', ['pageSlug' => 'area'])

@section('content')
    <div class="row">
        <div class="modal fade" id="addArea" tabindex="-1" role="dialog" aria-labelledby="addAreaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addAreaLabel">Create Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <form method="post" action="{{ route('area.create') }}">
                    @csrf
                    <label for="name">Area</label>
                    <input class="form-control" type="text" name="name" id="name" style="color: black" required/>
                
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
                </div>
            </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addArea">
                Create Area
              </button>
            <div class="card ">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="card-header">
                    <h4 class="card-title">Active Data List</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="stripe" id="list_table">
                            <thead class=" text-primary">
                                <tr>
                                    <th>
                                        No
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


    <script src="{{ asset('black') }}/js/plugins/chartjs.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('#list_table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [20, 40, 60, 80, 100],
            ajax: "{{ route('area.list') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action', width: 200},
            ]
        });
        });
    </script>
