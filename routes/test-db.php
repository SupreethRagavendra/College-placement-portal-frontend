<?php

// Test database connection
Route::get('/test-db', function () {
    try {
        $pdo = new PDO(
            "pgsql:host=db.wkqbukidxmzbgwauncrl.supabase.co;port=5432;dbname=postgres;sslmode=require",
            "postgres",
            "Supreeeth24#"
        );
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Connected to Supabase PostgreSQL successfully!',
            'version' => $version
        ]);
        
    } catch (PDOException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Connection failed: ' . $e->getMessage()
        ]);
    }
});
