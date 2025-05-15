
    <style>
        .star-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: fit-content;
            font-size: 20px;
        }

        .star-container:hover{
            cursor: pointer;
        }

        .checked {
            background: linear-gradient(to bottom right, rgb(0, 132, 255), rgb(42, 50, 87));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .unchecked{
            color: #ccc;
        }

        .star{
            padding: 2px;
            transition: all 0.3s;
        }

        .star:hover{
            transform: scale(1.1) rotate(10deg);
            transition: 0.3s, 0.5s;
        }
    </style>

    <div class="star-container" onload="fixStars()">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <span class="fa fa-star checked star" onclick="checkStar(this)"></span>
        <span class="fa fa-star checked star" onclick="checkStar(this)"></span>
        <span class="fa fa-star checked star" onclick="checkStar(this)"></span>
        <span class="fa fa-star unchecked star" onclick="checkStar(this)"></span>
        <span class="fa fa-star unchecked star" onclick="checkStar(this)"></span>
    </div>

    <script>
        function checkStar(star){
            stars = document.querySelectorAll(".star")
            under = true
            stars.forEach(element => {
                if(under){
                    element.classList.remove("unchecked")
                    element.classList.add("checked")
                }else{
                    element.classList.remove("checked")
                    element.classList.add("unchecked")
                }

                if(star==element){
                    under=false;
                }
                
            });
        }
    </script>