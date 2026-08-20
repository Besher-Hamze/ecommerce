<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        font-family: 'Jost', sans-serif;
        /* background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e); */
    }

    .main {
        width: 500px;
        height: 650px;
        background: red;
        overflow: hidden;
        background: url("https://doc-08-2c-docs.googleusercontent.com/docs/securesc/68c90smiglihng9534mvqmq1946dmis5/fo0picsp1nhiucmc0l25s29respgpr4j/1631524275000/03522360960922298374/03522360960922298374/1Sx0jhdpEpnNIydS4rnN4kHSJtU1EyWka?e=view&authuser=0&nonce=gcrocepgbb17m&user=03522360960922298374&hash=tfhgbs86ka6divo3llbvp93mg4csvb38") no-repeat center/ cover;
        border-radius: 10px;
        box-shadow: 5px 20px 50px #000;
    }

    #chk {
        display: none;
    }

    .signup {
        position: relative;
        width: 100%;
        height: 100%;
    }

    label {
        color:#d46a92;
        font-size: 2.3em;
        justify-content: center;
        display: flex;
        margin: 60px;
        font-weight: bold;
        cursor: pointer;
        transition: .5s ease-in-out;
    }

    input {
        width: 60%;
        height: 40px;
        background: #e0dede;
        justify-content: center;
        display: flex;
        margin: 20px auto;
        padding: 10px;
        border: none;
        outline: none;
        border-radius: 5px;
    }

    button {
        width: 60%;
        height: 40px;
        margin: 10px auto;
        justify-content: center;
        display: block;
        color: #ffff;
        background: #d46a92;
        font-size: 1em;
        font-weight: bold;
        margin-top: 40px;
        outline: none;
        border: none;
        border-radius: 5px;
        transition: .2s ease-in;
        cursor: pointer;
    }

    /* button:hover {
        background: #e81c4f;
    } */
  .color
	{
		color:red;
		padding-right:100px;
	}
    .login {
        height: 460px;
        background: #d46a92;
        border-radius: 60% / 10%;
        transform: translateY(-180px);
        transition: .8s ease-in-out;
    }

    .login label {
        color: red;
        transform: scale(.6);
    }

    #chk:checked~.login {
        transform: translateY(-600px);
    }

    #chk:checked~.login label {
        transform: scale(1);
    }

    #chk:checked~.signup label {
        transform: scale(.6);
    }
	.link
	{color:#ffff;
	
	}
	form{
	padding-top:70px;}
</style>
<!DOCTYPE html >
<html>

<head>
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" type="text/css" href="slide navbar style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="main" dir="rtl">
		
        <input type="checkbox" id="chk" aria-hidden="true">
       <!-- <div class="signup">
           <form method="POST" action="{{ route('register') }}">
                @csrf
                <label for="chk" aria-hidden="true">Sign up</label>
                <input type="text" name="name" placeholder="User name" required="" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"> @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('name') }}</strong>
                                    </span>
                 @endif
                <input type="email" name="email" placeholder="Email" required="" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"> @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('email') }}</strong>
                                    </span>
                 @endif
                <input type="password" name="password" placeholder="Password" required="" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"> @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('password') }}</strong>
                                    </span>
                 @endif
                <input type="password" name="password_confirmation" placeholder="password_confirmation" required="" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"> 
			   @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('email') }}</strong>
                                    </span>
                 @endif
                <button type="submit"> Register</button>
            </form>
			
        </div>-->
        <div class="signup">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label>{{__('login.sign_in')}}</label>
                <input type="email" name="email" placeholder="{{__('login.email')}}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" required="">
				 @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('email') }}</strong>
                                    </span>
                 @endif
                <input type="password" name="password" placeholder="{{__('login.password')}}" required="" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" >
				 @if($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong class="color">{{ $errors->first('password') }}</strong>
                                    </span>
                 @endif
                <button type="submit">{{__('login.login')}}</button>

            </form>
        </div>
		<div class="login">
                <label  aria-hidden="true"><a class="link" href="{{route('register')}}">{{__('login.register')}}</a></label>
        </div>
    </div>

</body>
</html>


