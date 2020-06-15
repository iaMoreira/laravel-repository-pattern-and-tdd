# Conceito
    - MVC
        - View
        - Controller
        - Service
        - Repository
        - Model

    - Product
        - ProductController
        - ProductService
        - Proc 
```php
Product::where('id', 1)->where('status', true)->first();
Product::where('company_id', $company_id) // verificar se o produto pertence a empresa
    ->where('status', true)  // verificar se está ativo
    ->first();
```
