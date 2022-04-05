@extends('layouts.app', ['page' => __('Site Detail'), 'pageSlug' => 'site'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
            @endif
            @if (session('danger'))
                    <div class="alert alert-danger">
                        {{ session('danger') }}
                    </div>
            @endif
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">{{$site->sitecode}} <span><i class="tim-icons icon-settings clickable-clear" id="icon-settings"></i></span></h3>
                            
                        </div>
                    </div>
                </div>
                <form method="post" action="{{ route('site.update', $site->id) }}">
                    @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="sitecode">Site Code</label>
                            <input type="text" class="form-control" placeholder="SiteCode" name="sitecode" id="sitecode" value="{{$site->sitecode}}" required/>
                        </div>
                        <div class="col-md-4">
                            <label for="duid">DUID</label>
                            <input type="text" class="form-control" placeholder="DUID" name="duid" id="duid" value="{{$site->duid}}" required  />
                        </div>
                        <div class="col-md-4">
                            <label for="area">Area</label>
                            <select class="form-control" name="area" id="area">
                                @foreach ($area as $areas)
                                    <option value="{{$areas->id}}" style="background: rgb(51, 50, 50)">{{$areas->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="options" style="display:none;">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="submit" class="button btn-sm btn-success">Save</button>
                            <button type="button" id="cancel" class="button btn-sm btn-danger">Cancel</button>
                        </div>
                    </div>
                </div>
                </form>
                <div class="card-footer py-4">
                    <nav class="d-flex justify-content-end" aria-label="...">
                        
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="card-title"><b>Requirements </b>
                                <span><i class="tim-icons icon-simple-add clickable adding" id="icon-step"></i></span>
                            {{-- <hr style="border: 1px solid rgb(150, 63, 135);"> --}}
                            <button type="button" class="btn btn-secondary btn-sm pull-right" id="togDelete">Delete</button>
                            <button type="button" class="btn btn-secondary btn-sm pull-right" id="togCancel" style="display: none">Cancel</button></h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($step as $steps)
                    <div class="row">
                        <div class="col-md-12 text-light">
                            <hr style="border: 1px solid rgb(150, 63, 135);">
                            @php
                                $step_active = 0;
                            @endphp
                            {{$steps->name}} 
                            <span><i class="tim-icons icon-simple-add clickable adding" id="icon-step" onclick="task({{$steps->id}})"></i></span>
                            <span><i class="tim-icons icon-trash-simple clickable-clear trash" id="icon-step" onclick="delete_step({{$steps->id}})" style="display: none"></i></span>
                            <hr style="border: 1px solid rgb(150, 63, 135);">
                            {{-- task --}}
                            @foreach ($task as $tasks)
                                @if ($tasks->step_id === $steps->id)
                                    <div class="row">
                                        <div class="col-md-1"></div>
                                        <div class="col-md-9">(
                                            @php
                                             $active = 0;   
                                            @endphp
                                            @foreach($task_active as $count)
                                            @if($count->task_id === $tasks->taskid)
                                            @php
                                                $active = $count->count;   
                                            @endphp
                                            @endif
                                            @endforeach
                                            {{$active}} /
                                            @foreach($task_count as $count)
                                                @if($count->task_id === $tasks->taskid)
                                                    {{$count->count}}
                                                    {{-- check if count and active is equal --}}
                                                    @php
                                                        if($count->count === $active){
                                                            $step_active += 1;
                                                        }
                                                    @endphp
                                                @endif
                                            @endforeach
                                            )
                                            {{$tasks->task_name}} <span><i class="tim-icons icon-simple-add clickable adding" id="icon-step" onclick="item({{$tasks->taskid}})"></i></span>
                                            <span><i class="tim-icons icon-trash-simple clickable-clear trash" onclick="delete_task({{$tasks->taskid}})" id="icon-step" style="display: none"></i></span>
                                            {{-- <hr style="border: 1px solid rgb(150, 63, 135);"> --}} <br>
                                            {{-- task item --}}
                                            @foreach ($task_item as $item)
                                                @if ($item->task_id === $tasks->taskid)
                                                <br>
                                                    <div class="row">
                                                        <div class="col-md-1 text-right">
                                                            <i class="tim-icons icon-trash-simple clickable-clear trash" style="margin-top:60px;font-size:15px;display:none;" onclick="delete_item({{$item->id}})"></i>
                                                        </div>
                                                        <div class="col-md-11">
                                                            <div class="card border-bottom border-left 
                                                            @if($item->status === 0)
                                                            border-secondary
                                                            @elseif($item->status === 1)
                                                            border-success
                                                            @elseif($item->status === 2)
                                                            border-danger
                                                            @endif
                                                            ">
                                                                <div class="card-body">
                                                                  <h5 class="card-title">{{$item->taskitem}}</h5>
                                                                  <br><br>
                                                                  <h6 class="card-subtitle mb-2 text-muted">Remarks:<br>
                                                                    <input type="text" class="form-control border-0" id="a{{$item->id}}" value="{{$item->remarks}}" onchange="edit_item({{$item->id}})"/>
                                                                  </h6>
                                                                </div>
                                                              </div>
                                                            {{-- <hr style="border: 1px solid rgb(9, 18, 29);"> --}}<br>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                            {{-- end task item --}}
                                        </div>
                                        <div class="col-md-2">
                                            <h5 class="text-center" style="padding-bottom: 5px;">Status</h5>
                                            <hr style="border: 1px solid rgb(150, 63, 135);">
                                            {{-- task item button--}}
                                            @foreach ($task_item as $item)
                                                @if ($item->task_id === $tasks->taskid)
                                                <br>
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    @if($item->status === 0)
                                                                    <a href="{{route('aprrove', $item->id)}}" class="btn btn-sm btn-success float-center">Approve</a>
                                                                    <a href="{{route('reject', $item->id)}}" class="btn btn-sm btn-danger float-center">Reject</a>
                                                                    @endif
                                                                    @if($item->status === 1)
                                                                    <a href="{{route('reject', $item->id)}}" class="btn btn-sm btn-danger float-center">Reject</a>
                                                                    @endif
                                                                    @if($item->status === 2)
                                                                    <a href="{{route('aprrove', $item->id)}}" class="btn btn-sm btn-success float-center">Approve</a>
                                                                    @endif
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br><br>
                                                @endif
                                            @endforeach
                                            {{-- end task item --}}
                                        </div>
                                    </div>
                                    <br><br>
                                @endif
                            @endforeach
                            {{-- end task --}}
                        </div>
                    </div>
                    <br><br><br>
                    @endforeach
                </div>
                <div class="card-footer py-4">
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" id="modalbtn" data-target="#containerModal" style="display: none">
        Launch
    </button>
    <button type="button" class="btn btn-primary" data-toggle="modal" id="deletemodalbtn" data-target="#deleteModal" style="display: none">
        Launch
    </button>
    
    <!-- Modal -->
    <div class="modal fade" id="containerModal" tabindex="-1" role="dialog" aria-labelledby="containerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: black">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               
                {{-- adding Step --}}
                <form method="post" action="{{route("step.create", $site->id)}}">
                    @csrf
                    <div class="row" id="add-div" style="display: none">
                            <label for="step">Step</label>            
                            <input class="form-control border-secondary" name="step" id="step" required/>
                            <button type="submit" class="btn btn-success btn-md" style="margin-top:20px">Add</button>
                    </div>
                </form>
               
                {{-- adding task --}}
                <form method="post" action="{{route("task.create")}}">
                    @csrf
                    <div class="row" id="add-task" style="display: none">
                            <input class="form-control border-secondary" name="step_id" id="step_id" hidden/>
                            <label for="task">Task</label>   
                            <input class="form-control border-secondary" name="task" id="task" required/>
                            <button type="submit" class="btn btn-success btn-md" style="margin-top:20px">Add</button>
                    </div>
                </form>

                 {{-- adding task-item --}}
                 <form method="post" action="{{route("item.create")}}">
                    @csrf
                    <div class="row" id="add-task-item" style="display: none">
                            <input class="form-control border-secondary" name="task_id" id="task_id" hidden/>
                            <label for="task">Task</label>   
                            <input class="form-control border-secondary" name="task_item" id="task-item" required/>
                            <label for="task">Remarks</label>   
                            <input class="form-control border-secondary" name="remarks" id="remarks"/>
                            <button type="submit" class="btn btn-success btn-md" style="margin-top:20px">Add</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="containerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: black">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
               
                {{-- deleting Step --}}
                <form method="post" action="{{route("step.delete", $site->id)}}">
                    @csrf
                    <div class="row" id="delete-div" style="display: none">
                        <h4 class="text-center"><span><i class="tim-icons icon-alert-circle-exc text-danger" style="font-size: 40px"></i></span> Are you sure you want to permanently remove this item?</h4>            
                            <input class="form-control border-secondary" name="step_id_delete" id="step_id_delete" hidden/>
                            <div class="col-sm-6 text-left">
                                <button type="submit" class="btn btn-danger btn-md" style="margin-top:20px">Delete</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-secondary btn-md cancel" style="margin-top:20px">Cancel</button>
                            </div>
                    </div>
                </form>
               
                {{-- deleting task --}}
                <form method="post" action="{{route("task.delete")}}">
                    @csrf
                    <div class="row" id="delete-task" style="display: none">
                        <h4 class="text-center"><span><i class="tim-icons icon-alert-circle-exc text-danger" style="font-size: 40px"></i></span> Are you sure you want to permanently remove this item?</h4>
                            <input class="form-control border-secondary" name="task_id_delete" id="task_id_delete" hidden/>
                            <div class="col-sm-6 text-left">
                                <button type="submit" class="btn btn-danger btn-md" style="margin-top:20px">Delete</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-secondary btn-md cancel" style="margin-top:20px">Cancel</button>
                            </div>
                    </div>
                </form>

                 {{-- deleting task-item --}}
                 <form method="post" action="{{route("item.delete")}}">
                    @csrf
                    <div class="row" id="delete-task-item" style="display: none">
                            <h4 class="text-center"><span><i class="tim-icons icon-alert-circle-exc text-danger" style="font-size: 40px"></i></span> Are you sure you want to permanently remove this item?</h4>
                            <input class="form-control border-secondary" name="task_id_del" id="task_id_del" hidden/>
                            <div class="col-sm-6 text-left">
                                <button type="submit" class="btn btn-danger btn-md" style="margin-top:20px">Delete</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button type="button" class="btn btn-secondary btn-md cancel" style="margin-top:20px">Cancel</button>
                            </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready( function () {
            //
            $("#sitecode, #duid, #area").prop( "disabled", true );
            $("#options").hide();
            $("#area").val({{$site->area_id}});
            $("#add-div").hide();
            $(".trash").hide();

            $("#icon-settings").click(function(){
               $("#sitecode, #duid, #area").prop( "disabled", false );
               $("#options").show();
            })

            $("#cancel").click(function(){
               $("#sitecode, #duid, #area").prop( "disabled", true );
               $("#options").hide();
            })

            $("#icon-step").click(function(){
                $("#modalbtn").click();
                $("#add-task").hide();
                $("#add-task-item").hide();
                if($("#add-div").is(":visible")){
                    $("#add-div").hide();
                } else{
                    $("#add-div").show();
                }
            })

            $("#togDelete").click(function(){
                $('.trash').show();
                $('.adding').hide();
                $("#togDelete").hide();
                $("#togCancel").show();
            })

            $("#togCancel").click(function(){
                $('.trash').hide();
                $('.adding').show();
                $("#togDelete").show();
                $("#togCancel").hide();
            })

            $('.cancel').click(function($e){
                $e.preventDefault();
                $('#deletemodalbtn').click();
            })
            // 
        });

        function task(x){
            $("#modalbtn").click();
            $("#add-div").hide();
            $("#add-task-item").hide();
            if($("#add-task").is(":visible")){
                $("#add-task").hide();
                $("#task").val("");
            } else{
                $("#add-task").show();
                $("#step_id").val(x);
            }
        }

        function item(x){
            $("#modalbtn").click();
            $("#add-div").hide();
            $("#add-task").hide();
            if($("#add-task-item").is(":visible")){
                $("#add-task-item").hide();
                $("#task_item").val("");
                $("#remarks").val("");
            } else{
                $("#add-task-item").show();
                $("#task_id").val(x);
            }
        }

        function edit_item(x){
            $.ajax({
            type:"Post",
            url: "/remarks/"+x,
            data: {
                'remarks': $("#a"+x).val(),
                '_token': "{{ csrf_token() }}",
                },
            });
        }

        function delete_item(x){
            $('#deletemodalbtn').click();
            $('#delete-task').hide();
            $("#delete-div").hide();
            if($("#add-task").is(":visible")){
                $('#delete-task-item').hide();
                $('#task_id_del').val("");
            } else{
                $('#delete-task-item').show();
                $('#task_id_del').val(x);
            }
        }

        function delete_task(x){
            $('#deletemodalbtn').click();
            $('#delete-task-item').hide();
            $("#delete-div").hide();
            if($("#add-task").is(":visible")){
                $("#delete-task").hide();
                $("#task_id_delete").val("");
            } else{
                $("#delete-task").show();
                $("#task_id_delete").val(x);
            }
        }

        function delete_step(x){
            $('#deletemodalbtn').click();
            $('#delete-task-item').hide();
            $("#delete-task").hide();
            if($("#add-task").is(":visible")){
                $("#delete-div").hide();
                $("#step_id_delete").val("");
            } else{
                $("#delete-div").show();
                $("#step_id_delete").val(x);
            }
        }
        </script>
@endsection
