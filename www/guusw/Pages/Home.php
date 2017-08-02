<style>
.about
{
    display: flex;
    align-items: flex-start;
    justify-content: center;
}
.about .desc
{
    flex: 3;
}
@media screen and (max-width: 600px)
{
    .about
    {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .about .desc
    {
        flex: 1;
    }
    .about .main_image
    {
        display:none;
    }
}
</style>
<table class="main_split">
<tr>
    <td>
        <h1>About Me</h1>
        <div class="indent">
        <div class="about">
            <div class="desc">
                <p>
                Hello, my name is Guus Waals.<br/>
                I just graduated as a programmer at the NHTV University in Breda, The Netherlands.</p>
                <p>
                I have been programming for about 5 years and have always had an interest in video games, their technical details and how computers work.
                <p>
                When I started off, I did mostly graphics programming using SDL, OpenGL and DirectX libraries, creating rendering frameworks in C++.
                This helped me to become familiar with C++ in both 2D and 3D rendering.</br>
                Besides graphics programming, the last 3 years I have also enjoyed working on other subjects that are useful in video game development, such as: 
                Software Audio Mixing, Physics Simulation, Data Driven engines, Tools, GUI systems and also Gameplay and AI programming.
                </p>
                <p>
                Feel free to contact me by LinkedIn, GitHub or e-mail.
                </p>
            </div>
            <div class="main_image">
                <img src="Images/hat.jpg">
                </img>
            </div>
        </div>
        </div>
    </td>
    <!--<td>
    <?php //$gallery_path = "Images/Raytracer"; require "Gallery.php"; ?>
    </td>-->
</tr>
</table>
