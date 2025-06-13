class Carrusel {

    constructor(){
        this.cargarCarrusel();
    }

    cargarCarrusel() {
        const slides = document.querySelectorAll('img');
        const nextSlide = document.querySelector('article>button:nth-of-type(1)');

        let curSlide = 0;
        let maxSlide = slides.length-1;

        nextSlide.addEventListener('click', function(){
            if(curSlide === maxSlide){
                curSlide = 0;
            }else{
                curSlide++;
            }

            slides.forEach((slide, indx) =>{
                var trans = 100*(indx - curSlide);
                $(slide).css('transform', 'translateX(' + trans + '%)');
            });
        });

        const prevSlide = document.querySelector('article>button:nth-of-type(2)');

        prevSlide.addEventListener('click', function(){
            if(curSlide === 0){
                curSlide = maxSlide;
            }else{
                curSlide--;
            }

            slides.forEach((slide, indx) => {
                var trans = 100*(indx - curSlide);
                $(slide).css('transform', 'translateX(' + trans + '%)')
            });
        });
    }

}