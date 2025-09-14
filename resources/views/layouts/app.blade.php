<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carista News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0891b2;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 1200px;
            padding: 2rem;
        }

        .search-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 2rem;
        }

        .search-input {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .article-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 1.5rem;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .article-meta {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .article-link {
            background: linear-gradient(135deg, var(--success-color), #10b981);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .article-link:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        /* Mobile pagination styles */
        @media (max-width: 768px) {
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }

            .pagination .page-link {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
                margin: 0 1px;
                min-width: 2.5rem;
                text-align: center;
            }

            .pagination .page-item:first-child .page-link,
            .pagination .page-item:last-child .page-link {
                padding: 0.4rem 0.8rem;
            }

            .pagination .page-item.disabled .page-link {
                opacity: 0.5;
                pointer-events: none;
            }

            /* Hide some page numbers on very small screens */
            @media (max-width: 480px) {
                .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.disabled) {
                    display: none;
                }
                
                .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.disabled):nth-child(2),
                .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.disabled):nth-last-child(2) {
                    display: block;
                }
            }
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
