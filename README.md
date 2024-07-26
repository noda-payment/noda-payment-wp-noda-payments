# Running coding standards checks and fixes:
1. Install dev dependencies: ```composer install --ignore-platform-reqs```
2. Run check and fix coding standards: ``` composer fix:standards```

# Getting vendor dependencies ready for plugin publishing
```
composer install --no-dev --ignore-platform-reqs --optimize-autoloader
```
