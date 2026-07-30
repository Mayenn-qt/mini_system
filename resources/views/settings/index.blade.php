@extends('layouts.app')

@section('title', 'System Settings')
@section('header_title', 'Configuration Panel')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Store Settings (Owner Only) -->
        @if(auth()->user()->isOwner())
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 pb-0">
                        <h5 class="fw-semibold text-accent m-0">Store Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Store Brand Name</label>
                                <input type="text" name="store_name" class="form-control" value="{{ $storeName }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address Location</label>
                                <input type="text" name="store_address" class="form-control" value="{{ $storeAddress }}">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="store_phone" class="form-control" value="{{ $storePhone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" name="store_email" class="form-control" value="{{ $storeEmail }}">
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">SMS API Key</label>
                                    <input type="text" name="sms_gateway_api_key" class="form-control" value="{{ $smsApiKey }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">SMS Sender ID</label>
                                    <input type="text" name="sms_sender_id" class="form-control" value="{{ $smsSenderId }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                Save Store Configuration
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Profile Settings -->
        <div class="col-lg-6">
            <!-- Account Settings Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 pb-0">
                    <h5 class="fw-semibold text-accent m-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.account') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Update Profile Details
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 pb-0">
                    <h5 class="fw-semibold text-accent m-0">Change Account Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Update Account Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
