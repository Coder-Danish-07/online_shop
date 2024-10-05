@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('account.profile')}}">My Account</a></li>
                <li class="breadcrumb-item">Settings</li>
            </ol>
        </div>
    </div>
</section>

<section class=" section-11 ">
    <div class="container  mt-5">
        @include('front.account.common.message')
        <div class="row">
            <div class="col-md-3">
                @include('front.account.common.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                    </div>
                    <form action="" name="profileForm" id="profileForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">               
                                    <label for="name">Name</label>
                                    <input type="text" value="{{ $user->name }}" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">            
                                    <label for="email">Email</label>
                                    <input type="text" value="{{ $user->email }}" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">                                    
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" value="{{ $user->phone }}" id="phone" placeholder="Enter Your Phone" class="form-control">
                                    <p></p>
                                </div>

                                {{-- <div class="mb-3">                                    
                                    <label for="phone">Address</label>
                                    <textarea name="address" id="address" class="form-control" cols="30" rows="5" placeholder="Enter Your Address"></textarea>
                                </div> --}}

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                    </div>
                    <form action="" name="addressForm" id="addressForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3 col-md-6">               
                                    <label for="first_name">Firt Name</label>
                                    <input type="text" value="{{ (!empty($address->first_name)) ? $address->first_name : ''}}" name="first_name" id="first_name" placeholder="Enter Your First Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">               
                                    <label for="last_name">Last Name</label>
                                    <input type="text" value="{{ (!empty($address->last_name)) ? $address->last_name : ''}}" name="last_name" id="last_name" placeholder="Enter Your Last Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">            
                                    <label for="email">Email</label>
                                    <input type="text" value="{{ (!empty($address->email)) ? $address->email : ''}}" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">                                    
                                    <label for="mobile">Mobile No</label>
                                    <input type="text" name="mobile" value="{{ (!empty($address->mobile)) ? $address->mobile : ''}}" id="mobile" placeholder="Enter Your mobile" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">            
                                    <label for="country_id">Country</label>
                                    <select name="country_id" id="country_id" class="form-control">
                                        <option value="">Select a Country</option>
                                        @if($countries->isNotEmpty())
                                        @foreach($countries as $country)
                                        <option {{ (!empty($address) && $address->country_id == $country->id) ? 'selected' : ''}} value="{{ $country->id }}">{{$country->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                                <div class="mb-3">            
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" cols="30" rows="5" class="form-control">{{ (!empty($address->address)) ? $address->address : ''}}</textarea>
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">            
                                    <label for="apartment">Apartment</label>
                                    <input type="text" value="{{ (!empty($address->apartment)) ? $address->apartment : ''}}" name="apartment" id="apartment" placeholder="Apartment" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">            
                                    <label for="city">City</label>
                                    <input type="text" value="{{ (!empty($address->city)) ? $address->city : ''}}" name="city" id="city" placeholder="Enter Your city" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">            
                                    <label for="state">State</label>
                                    <input type="text" value="{{ (!empty($address->state)) ? $address->state : ''}}" name="state" id="state" placeholder="Enter Your state" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">            
                                    <label for="zip">Zip</label>
                                    <input type="text" value="{{ (!empty($address->zip)) ? $address->zip : ''}}" name="zip" id="zip" placeholder="Enter Your zip" class="form-control">
                                    <p></p>
                                </div>

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('customJs')
<script>
    $("#profileForm").submit(function(event){
        event.preventDefault();

        $.ajax({
            url: '{{ route("account.updateProfile")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType:'json',
            success: function(response){
                if(response.status == true){
                    $("#name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#profileForm #email").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#phone").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    
                    window.location.href = "{{ route('account.profile')}}";

                }else{
                    var errors = response.errors;

                    if(errors.name){
                        $("#name").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.name);
                    }
                    else{
                        $("#name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.email){
                        $("#profileForm #email").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.email);
                    }
                    else{
                        $("#profileForm #email").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.phone){
                        $("#phone").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.phone);
                    }
                    else{
                        $("#phone").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }
                   
                }
            }
        });
    });

    $("#addressForm").submit(function(event){
        event.preventDefault();

        $.ajax({
            url: '{{ route("account.updateAddress")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType:'json',
            success: function(response){
                if(response.status == true){

                    $("#first_name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#last_name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#addressForm #email").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#mobile").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#country_id").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#address").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#city").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#state").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');

                    $("#zip").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    
                    window.location.href = "{{ route('account.profile')}}";

                }else{
                    var errors = response.errors;

                    if(errors.first_name){
                        $("#first_name").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.first_name);
                    }
                    else{
                        $("#first_name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.last_name){
                        $("#last_name").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.last_name);
                    }
                    else{
                        $("#last_name").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.email){
                        $("#addressForm #email").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.email);
                    }
                    else{
                        $("#addressForm #email").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.mobile){
                        $("#mobile").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.mobile);
                    }
                    else{
                        $("#mobile").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.country_id){
                        $("#country_id").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.country_id);
                    }
                    else{
                        $("#country_id").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.address){
                        $("#address").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.address);
                    }
                    else{
                        $("#address").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.city){
                        $("#city").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.city);
                    }
                    else{
                        $("#city").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.state){
                        $("#state").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.state);
                    }
                    else{
                        $("#state").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.zip){
                        $("#zip").addClass("is-invalid")
                        .siblings('p').addClass('invalid-feedback').html(errors.zip);
                    }
                    else{
                        $("#zip").removeClass("is-invalid")
                        .siblings('p').removeClass('invalid-feedback').html('');
                    }
                   
                }
            }
        });
    });
</script>
@endsection