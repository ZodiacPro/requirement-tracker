@extends('layouts.app', ['page' => __('Site List'), 'pageSlug' => 'site'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Button modal -->
            <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#addSite">
                Create Site
            </button>
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
                        <table class="display nowrap" id="list">
                            <thead>
                            <tr>
                            <th>Id</th>
                            <th>Site Code</th>
                            <th>DUID</th>
                            <th>Area</th>
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
      <!-- Modal -->
    <div class="modal fade" id="addSite" tabindex="-1" role="dialog" aria-labelledby="exampleaddSite" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleaddSite">New Site</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            <form method="post" action="{{ route('site.create') }}">
                @csrf
                <label for="sitecode">Site Code</label>
                <input type="text" class="form-control" placeholder="SiteCode" name="sitecode" id="sitecode" style="color:black;" required/>
                <BR />
                <label for="duid">DUID</label>
                <input type="text" class="form-control" placeholder="DUID" name="duid" id="duid" style="color:black;" required  />
                <BR />
                <label for="area">Area</label>
                <select class="form-control" name="area" id="area" style="color:black;">
                    @foreach ($area as $areas)
                        <option value="{{$areas->id}}">{{$areas->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
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
                var table = $('#list').DataTable();
                table.destroy();
                $('#list').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 20,
                lengthMenu: [20, 40, 60, 80, 100],
                ajax: "{{ url('site') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'sitecode', name: 'sitecode' },
                    { data: 'duid', name: 'duid' },
                    { data: 'area_name', name: 'area_name' },
                    {data: 'action', name: 'action', orderable: false, width: 400},
                ],
                order: [[0, 'desc']]
            });
            });
            $('#proceed').click();
        });
        </script>
@endsection

