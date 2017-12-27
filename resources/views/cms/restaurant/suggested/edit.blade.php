@extends('cms.layouts.master')
@section('content')
    <h2>Edit Suggested Restaurant - {{ $restaurant['name'] }}</h2>
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (!$restaurant)
        <div class="alert alert-danger">Suggested Restaurant not found</div>
    @else
        <form class="form-horizontal" action="{{ URL::to('cms/restaurant/suggested/edit/' . $id) }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Restaurant Name</label>
                <div class="col-sm-6">
                    <input class="form-control" id="name" name="name" type="text" placeholder="Restaurant Name" value="{{ $restaurant->name }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="address" class="col-sm-2 control-label">Restaurant Address</label>
                <div class="col-sm-6">
                    <input class="form-control" id="address" name="address" type="text" placeholder="Restaurant Address" value="{{ $restaurant->address }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="restaurant-telephone" class="col-sm-2 control-label">Phone Number</label>
                <div class="col-sm-6">
                    <input class="form-control" id="restaurant-telephone" name="telephone" type="text" placeholder="Restaurant Phone Number" value="{{ $restaurant->telephone }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="budget" class="col-sm-2 control-label">Budget</label>
                <div class="col-sm-6">
                    <input class="form-control" id="budget" name="budget" value="{{ $restaurant->budget }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="latitude" class="col-sm-2 control-label">Latitude</label>
                <div class="col-sm-6">
                    <input class="form-control" id="latitude" name="latitude" type="text" value="{{ $restaurant->latitude }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="longitude" class="col-sm-2 control-label">Longitude</label>
                <div class="col-sm-6">
                    <input class="form-control" id="longitude" name="longitude" type="text" value="{{ $restaurant->longitude }}" />
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="operating-time" class="col-sm-2 control-label">Operating Time</label>
                <div class="col-sm-6">
                    <input class="form-control" id="operating-time" name="operating_time" type="text" placeholder="Operating Time" value="{{ $restaurant->operating_time }}" />
                </div>
            </div>
            <div class="form-group">
                <label for="credit-card" class="col-sm-2 control-label">Credit Card</label>
                <div class="col-sm-6">
                    <input name="credit_card" value="0" type="hidden"/>
                    <input id="credit-card" name="credit_card" type="checkbox" value="1" {{ $restaurant->credit_card ? 'checked' : '' }} />
                </div>
            </div>
            <div class="form-group">
                <label for="smoking" class="col-sm-2 control-label">Smoking</label>
                <div class="col-sm-6">
                    <input name="smoking" value="0" type="hidden"/>
                    <input id="smoking" name="smoking" type="checkbox" value="1" {{ $restaurant->smoking ? 'checked' : '' }} />
                </div>
            </div>
            <div class="form-group">
                <label for="is-24hours" class="col-sm-2 control-label">Is 24 Hours</label>
                <div class="col-sm-6">
                    <input name="is_24hours" value="0" type="hidden"/>
                    <input id="is-24hours" name="is_24hours" type="checkbox" value="1" {{ $restaurant->is_24hours ? 'checked' : '' }} />
                </div>
            </div>
            <div class="form-group">
                <label for="can-dinein" class="col-sm-2 control-label">Can Dine In</label>
                <div class="col-sm-6">
                    <input name="can_dinein" value="0" type="hidden"/>
                    <input id="can-dinein" name="can_dinein" type="checkbox" value="1" {{ $restaurant->can_dinein ? 'checked' : '' }} />
                </div>
            </div>
            <div class="form-group">
                <label for="can-dineout" class="col-sm-2 control-label">Can Dine Out</label>
                <div class="col-sm-6">
                    <input name="can_dineout" value="0" type="hidden"/>
                    <input id="can-dineout" name="can_dineout" type="checkbox" value="1" {{ $restaurant->can_dineout ? 'checked' : '' }}>
                </div>
            </div>
            <div class="form-group">
                <label for="can-deliver" class="col-sm-2 control-label">Can Deliver</label>
                <div class="col-sm-6">
                    <input name="can_deliver" value="0" type="hidden"/>
                    <input id="can-deliver" name="can_deliver" type="checkbox" value="1" {{ $restaurant->can_deliver ? 'checked' : '' }}/>
                </div>
            </div>
            <div class="form-group">
                <label for="cuisines" class="col-sm-2 control-label">Cuisines</label>
                <div class="col-sm-6">
                    <input class="form-control" id="cuisines" name="cuisines" type="text" placeholder="cuisines" />
                    <p class="lead"><small>{{ $restaurant->cuisines }}</small></p>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#cuisines-modal">Choose</button>
                </div>
            </div>
            <div class="form-group">
                <label for="other-details" class="col-sm-2 control-label">Other Details</label>
                <div class="col-sm-6">
                    <input class="form-control" id="other-details" name="other_details" type="text" placeholder="Other Details" value="{{ $restaurant->other_details }}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <button class="btn btn-primary btn-block" type="submit" value="Submit">Submit</button>
                </div>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="cuisines-modal" role="dialog">
            <div class="modal-dialog modal-sm">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title">Cuisines</h3>
                    </div>
                    <div class="modal-body">
                         <div class="container">
                             @foreach ($categories as $category)
                                 <div class="row">
                                     <div class="col-sm-2">
                                         <label for="{{ $category->name }}">{{ $category->name }}</label>
                                     </div>
                                     <div class="col-sm-1">
                                         <input type="checkbox" name="cuisine" value="{{ $category->name }}"/>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                    </div>
                    <div class="modal-footer">
                        <button id="add-cuisines" type="button" class="btn btn-primary" data-dismiss="modal">Add</button>
                    </div>
                </div>

            </div>
        </div>
        <!-- /Modal -->
    @endif
@stop
