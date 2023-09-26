<h2>Add Catalog</h2>
<form action="/catalog/create" method="POST" enctype="multipart/form-data">
    <label>Category</label><br>
    <input type="radio" name="category" value="ANIME" required checked> Anime
    <input type="radio" name="category" value="DRAMA" required> Drama<br><br>

    <label>Title</label><br>
    <input type="text" name="title" maxlength="40" required><br><br>

    <label>Description</label><br>
    <textarea name="description" maxlength="255"></textarea><br><br>

    <label>Poster (Max 200MB)</label><br>
    <input type="file" name="poster" accept="image/*" required><br><br>

    <label>Video (Max 30 seconds)</label><br>
    <input type="file" name="video" accept="video/*"><br><br>

    <input type="submit" value="Submit">
</form>