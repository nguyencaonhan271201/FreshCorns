<?php
class Movies {
    public static function getTrendingMovies($conn) {
        $query = "SELECT movie_id, movie_type FROM posts WHERE movie_id IS NOT NULL GROUP BY movie_id ORDER BY COUNT(*) DESC LIMIT 20";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}