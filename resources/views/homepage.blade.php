<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $restaurant_name }} - Login Portal</title>

    @php($icon = \App\Model\BusinessSetting::where(['key' => 'fav_icon'])->first()?->value ?? '')
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/restaurant/' . $icon ?? '') }}">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
    
    <style>
        .homepage-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .homepage-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1200px;
            width: 100%;
            display: flex;
            min-height: 600px;
        }
        
        .homepage-left {
            flex: 1;
            background: linear-gradient(135deg, #039D55 0%, #00C851 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            color: white;
            text-align: center;
        }
        
        .homepage-right {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .restaurant-logo {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            margin-bottom: 30px;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
            padding: 20px;
        }
        
        .restaurant-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .restaurant-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }
        
        .login-section {
            text-align: center;
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1rem;
        }
        
        .login-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .login-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .login-btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .login-btn-branch {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .login-btn-chef {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .login-btn i {
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .features {
            margin-top: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .feature-item {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        .feature-item i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .feature-item h4 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .feature-item p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .homepage-card {
                flex-direction: column;
                margin: 10px;
            }
            
            .homepage-left,
            .homepage-right {
                padding: 40px 20px;
            }
            
            .restaurant-title {
                font-size: 2rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="homepage-container">
        <div class="homepage-card">
            <!-- Left Side - Branding -->
            <div class="homepage-left">
                <img class="restaurant-logo" 
                     src="{{ asset('storage/restaurant/' . $restaurant_logo) }}" 
                     alt="{{ $restaurant_name }}"
                     onerror="this.src='{{ asset('assets/admin/img/160x160/img2.jpg') }}'">
                
                <h1 class="restaurant-title">{{ $restaurant_name }}</h1>
                <p class="restaurant-subtitle">Complete Restaurant Management Solution</p>
                
                <div class="features">
                    <div class="feature-item">
                        <i class="tio-restaurant"></i>
                        <h4>Order Management</h4>
                        <p>Real-time order tracking</p>
                    </div>
                    <div class="feature-item">
                        <i class="tio-kitchen"></i>
                        <h4>Kitchen Display</h4>
                        <p>Efficient kitchen operations</p>
                    </div>
                    <div class="feature-item">
                        <i class="tio-analytics"></i>
                        <h4>Analytics</h4>
                        <p>Business insights & reports</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Options -->
            <div class="homepage-right">
                <div class="login-section">
                    <h2 class="login-title">Welcome Back</h2>
                    <p class="login-subtitle">Choose your role to access the system</p>
                    
                    <div class="login-buttons">
                        <a href="{{ route('admin.auth.login') }}" class="login-btn login-btn-admin">
                            <i class="tio-admin"></i>
                            <div>
                                <div>Admin Login</div>
                                <small>System Administration</small>
                            </div>
                        </a>
                        
                        <a href="{{ route('branch.auth.login') }}" class="login-btn login-btn-branch">
                            <i class="tio-store"></i>
                            <div>
                                <div>Branch Login</div>
                                <small>Branch Management</small>
                            </div>
                        </a>
                        
                        <a href="{{ route('chef.auth.login') }}" class="login-btn login-btn-chef">
                            <i class="tio-kitchen"></i>
                            <div>
                                <div>Chef Login</div>
                                <small>Kitchen Operations</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/toastr.min.js"></script>
    
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.login-btn');
            
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px) scale(1.02)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
