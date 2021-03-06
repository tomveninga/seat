@extends('layouts.masterLayout')

@section('html_title', 'User Profile')

@section('page_content')

<div class="row">

	<div class="col-md-6">
		<div class="panel panel-default">
		  <div class="panel-heading">
		    <h3 class="panel-title">
		    	<b>{{ $user->username }} ({{ $user->email }})</b>
		    	<span class="pull-right">
		    		Last Login: {{ $user->last_login }} ({{ Carbon\Carbon::parse($user->last_login)->diffForHumans() }})
		    	</span>
		    </h3>
		  </div>
		  <div class="panel-body">

		  	<div class="col-md-6">
			  	<p class="lead small">Account Settings</p>
			  	<a data-toggle="modal" data-target="#password-modal"><i class="fa fa-lock"></i> Change Password</a>
		  	</div>

		  	<div class="col-md-6">
			  	<p class="lead small">Group Memberships</p>
				  	@foreach($groups as $group)
				  		{{ $group->name }}<br>
				  	@endforeach
			  	</div>
			 	</div>

		  <div class="panel-footer">
		  	{{ $key_count }} Owned API Keys
		  	<span class="pull-right">
		  		@if (Sentry::getUser()->isSuperUser())
		  			<span class="label label-danger">Administrator Account</span>
		  		@endif
		  	</span>
		  </div>
		</div>
	</div>

</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-center">For any account related enquiries, including permissions amendments, please contact the SeAT administrator.</p>
	</div>
</div>

<!-- password reveal modal -->
<div class="modal fade" id="password-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><i class="fa fa-lock"></i> Change Password</h4>
            </div>
            <div class="modal-body">
              	<p class="text-center">
	              	{{ Form::open(array('action' => 'ProfileController@postChangePassword', 'class' => 'form-horizontal')) }}
						<fieldset>

						<div class="form-group">
						  <label class="col-md-4 control-label" for="oldPassword">Old Password</label>
						  <div class="col-md-6">
						    <div class="input-group">
						      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
						      {{ Form::password('oldPassword', array('id' => 'oldPassword', 'class' => 'form-control'), 'required', 'autofocus') }}
						    </div>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-md-4 control-label" for="newPassword">New Password</label>
						  <div class="col-md-6">
						    <div class="input-group">
						      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
						      {{ Form::password('newPassword', array('id' => 'newPassword', 'class' => ' form-control'), 'required') }}
						    </div>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-md-4 control-label" for="confirmPassword">Confirm Password</label>
						  <div class="col-md-6">
						    <div class="input-group">
						      <span class="input-group-addon"><i class="fa fa-lock"></i></span>
						      {{ Form::password('newPassword_confirmation', array('id' => 'confirmPassword', 'class' => ' form-control'), 'required') }}
						    </div>
						  </div>
						</div>

						<!-- Button -->
						<div class="form-group">
						  <label class="col-md-4 control-label" for="singlebutton"></label>
						  <div class="col-md-6">
						    {{ Form::submit('Change Password', array('class' => 'btn bg-olive btn-block')) }}
						  </div>
						</div>

						</fieldset>
					{{ Form::close() }}
              	</p>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@stop