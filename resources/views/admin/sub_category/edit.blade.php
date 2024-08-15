@extends('admin.layouts.app')

@section('content')
        <!-- Content Header (Page header) -->
        <section class="content-header">					
            <div class="container-fluid my-2">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Sub Category</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{route('sub-category.index')}}" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <form action="" name="SubCategoryForm" id="SubCategoryForm">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">								
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select a category</option>
                                        @if($categories->isNotEmpty())
                                        @foreach($categories as $category)
                                        <option {{ ($subCategory->category_id == $category->id) ? 'Selected':''}} value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{$subCategory->name}}" name="name" id="name" class="form-control" placeholder="Name">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" value="{{$subCategory->slug}}" name="slug" id="slug" class="form-control" placeholder="Slug">	
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{($subCategory->status == 1) ? 'Selected':''}} value="1">Active</option>
                                        <option {{($subCategory->status == 0) ? 'Selected':''}} value="0">Block</option>
                                        <p></p>
                                    </select>
                                </div>
                            </div>	
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="showHome">Show on Home</label>
                                    <select name="showHome" id="showHome" class="form-control">
                                        <option {{($subCategory->showHome == 'Yes') ? 'Selected':''}} value="Yes">Yes</option>
                                        <option {{($subCategory->showHome == 'No') ? 'Selected':''}} value="No">No</option>
                                        <p></p>
                                    </select>
                                </div>
                            </div>										
                        </div>
                    </div>							
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{route('sub-category.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </div>
        </form>
            <!-- /.card -->
        </section>
        <!-- /.content -->
@endsection

@section('customJS')
<script>

$("#SubCategoryForm").submit(function(event){
        event.preventDefault();
        var element = $(this);
        $("button[type=submit]").prop('disabled',true);
        $.ajax({
            url:'{{ route("sub-category.update",$subCategory->id)}}',
            type:'put',
            data:element.serializeArray(),
            dataType:'json',

            success:function(response){
                $("button[type=submit]").prop('disabled',false);
                if(response["status"] == true){
                    window.location.href="{{route('sub-category.index')}}";
                    $('#name').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html("");
                    
                    $('#slug').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html("");

                    $('#category').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html("");

                }
                else{
                    if(response['Nofound'] == true){
                    window.location.href="{{route('sub-category.index')}}";
                    }
                    var errors = response['errors'];
                if(errors['name']){
                    $('#name').addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors['name']);
                }
                else{
                    $('#name').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html(""); 
                }

                if(errors['slug']){
                    $('#slug').addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors['slug']);
                }
                else{
                    $('#slug').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html("");
                }

                if(errors['category']){
                    $('#category').addClass('is-invalid')
                    .siblings('p').addClass('invalid-feedback').html(errors['category']);
                }
                else{
                    $('#category').removeClass('is-invalid')
                    .siblings('p').removeClass('invalid-feedback').html("");
                }
                }
                

            },error:function(jqXHR, exception){
                console.log('something went wrong');
            }

        });
    });



$('#name').change(function(){
    element = $(this);
    $("button[type=submit]").prop('disabled',true);
    $.ajax({
        url:'{{route("getSlug")}}',
        type:'get',
        data:{title:element.val()},
        dataType:'json',
        success:function(response){
        $("button[type=submit]").prop('disabled',false);
            if(response['status'] == true){
                $('#slug').val(response['slug']);
            }
        }
    });
});
</script>
@endsection