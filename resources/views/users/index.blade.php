@extends('layouts.app', ['page' => __('User Profile'), 'pageSlug' => 'users'])

@section('content')
    <div class="row">
        <!-- Button trigger modal -->
        <button hidden type="button" id="btnmdl" class="btn btn-primary" data-toggle="modal" data-target="#addmodal">
        </button>
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Users</h4>
                        </div>
                        <div class="col-4 text-right">
                            <a href="#" id="proceed" class="btn btn-sm btn-primary" hidden>Reload</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="">
                        <table class="display nowrap" id="ajax-crud-datatable">
                            <thead>
                            <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Team</th>
                            <th>Action</th>
                            </tr>
                            </thead>
                            </table>
                    </div>
                </div>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready( function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
            $('#proceed').click(function(){
                var table = $('#ajax-crud-datatable').DataTable();
                table.destroy();
                $('#ajax-crud-datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                lengthMenu: [20, 40, 60, 80, 100],
                ajax: "{{ url('user') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'typename', name: 'typename' },
                    { data: 'teamname', name: 'teamname'},
                    {data: 'action', name: 'action', orderable: false, width: 400},
                ],
                order: [[0, 'desc']]
            });
            });
            $('#proceed').click();
        });
        </script>
@endsection

