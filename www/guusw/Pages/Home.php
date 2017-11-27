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
    padding-right: 1em;
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
                I just graduated for International Game Architecture and Design (IGAD) at NHTV in Breda, The Netherlands.</p>
                <p>
                Since I was young I was always playing with computers and other electronic devices, figuring out how they work, disassembling them and putting them back together.<br>
                When I came into contact with video games I was always wondering how they worked and wanted to create them myself.
                <p>
                I really started programming at around the age of 16, I mostly worked with SDL, OpenGL and DirectX together with C++ figuring out how games get their graphics on the screen and trying to do so myself.
                This helped me become familiar with C++ and various 2D and 3D rendering techniques.
                </p>
                <p>
                I decided to follow the IGAD course after finishing high school. Here, I have enjoyed working on various subjects related to video game development, such as: 
                Software Audio Mixing, Software Rasterization, Raytracing, Physics Simulation, Game Engine Architecture, Tools, GUI systems, AI and Gameplay programming.
                </p>
                <p>
                Feel free to contact me through LinkedIn, GitHub or e-mail.
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
