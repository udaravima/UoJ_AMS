<?php
include_once 'config.php';
include_once ROOT_PATH . '/php/include/header.php';
?>
<title>Temporary</title>
<?php
include_once ROOT_PATH . '/php/include/content.php';
?>
<h1>Temporary</h1>

<div class="container mt-5">
    <h1>Temp testing</h1>
    <input type="search" class="form-control" placeholder="Search User" aria-label="Search Course" aria-describedby="course-addon" id="search-user-for-course" data-bs-toggle="dropdown" data-id="#tempo" name="search-user-for-course">
    <select class="selectpicker" multiple aria-label="Default select example" id="tempo">
        <optgroup label="Hellow 1">
            <option value="1">One</option>
            <option value="2">Two</option>
        </optgroup>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
        <option value="4">Four</option>
    </select>
</div>

<div class="container mt-5">
    dropdown
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown button
        </button>
        <ul class="dropdown-menu inner" style="overflow:hidden auto; max-height:245px;" aria-labelledby="dropdownMenuButton1">
            <li class="dropdown-header">Dropdown header</li>
            <li><a class="dropdown-item selected" href="#"><span class="bs-ok-default check-mark"></span><span class="text">Helo</span></a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li class="dropdown-divider">Hello</li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
        </ul>
    </div>
</div>

<div class="container mt-5">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-target="#dd1" aria-expanded="false">DropDown</button>
        <div class="dropdown-menu" id="dd1">
            <div class="dropdown-header">Student</div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
            <div class="dropdown-divider"></div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" value="1" name="selCourse[]" id="cour1">
                <label for="cour1" class="form-check-label">Hellow</label>
            </div>
        </div>
    </div>
</div>
<script>
    // var selection1 = $('#tempo').selectpicker();
    // // selection.on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
    // //     console.log('clickedIndex: ' + clickedIndex);
    // //     console.log('isSelected: ' + isSelected);
    // //     console.log('previousValue: ' + previousValue);
    // // });
    // let searchBox = selection1.parent().find('.bs-searchbox input');
    // searchBox.on('keyup', function(e) {
    //     console.log('keyup: ' + e.target.value);
    // });
</script>
<?php
include_once ROOT_PATH . '/php/include/footer.php';
