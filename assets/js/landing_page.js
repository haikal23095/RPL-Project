document.addEventListener('DOMContentLoaded', function() {
            const carouselContainer = document.querySelector('.carousel-container');
            const carouselWrapper = document.querySelector('.carousel-wrapper');
            const leftArrow = document.getElementById('leftArrow');
            const rightArrow = document.getElementById('rightArrow');
            
            let carouselItems = Array.from(document.querySelectorAll('.carousel-item')); // Konversi NodeList ke Array
            const originalItemCount = carouselItems.length;

            // Kloning item untuk efek looping mulus
            // Duplikat item terakhir (untuk transisi ke kiri) dan prepend
            const lastItemClone = carouselItems[originalItemCount - 1].cloneNode(true);
            carouselWrapper.prepend(lastItemClone);

            // Duplikat item pertama (untuk transisi ke kanan) dan append
            const firstItemClone = carouselItems[0].cloneNode(true);
            carouselWrapper.append(firstItemClone);

            // Perbarui carouselItems untuk menyertakan kloning
            carouselItems = Array.from(document.querySelectorAll('.carousel-item'));
            
            // Indeks awal: 1 (item asli pertama setelah kloning terakhir)
            let currentIndex = 1; 

            // Fungsi untuk memperbarui tampilan carousel (ukuran, opacity, grayscale, dan posisi)
            function updateCarouselDisplay(smooth = true) {
                // Tentukan transisi
                carouselWrapper.style.transition = smooth ? 'transform 0.5s ease-in-out' : 'none';

                // Terapkan kelas 'active' ke item yang aktif dan hapus dari yang lain
                carouselItems.forEach((item, index) => {
                    if (index === currentIndex) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });

                // Paksa reflow untuk memastikan lebar elemen diperbarui setelah perubahan kelas
                void carouselWrapper.offsetWidth; 

                const activeItem = carouselItems[currentIndex];
                
                // Dapatkan posisi dan lebar elemen relatif terhadap viewport
                const carouselContainerRect = carouselContainer.getBoundingClientRect();
                const activeItemRect = activeItem.getBoundingClientRect();

                // Hitung pusat item aktif relatif terhadap viewport
                const activeItemCenterViewport = activeItemRect.left + (activeItemRect.width / 2);

                // Hitung pusat container carousel relatif terhadap viewport
                const containerCenterViewport = carouselContainerRect.left + (carouselContainerRect.width / 2);

                // Hitung seberapa banyak wrapper perlu digeser
                // Jika pusat item aktif di sebelah kanan pusat container, shiftNeeded akan positif
                // Maka wrapper perlu digeser ke kiri (translateX negatif)
                const shiftNeeded = activeItemCenterViewport - containerCenterViewport;
                
                // Dapatkan nilai transformX saat ini dari wrapper
                const currentTransformMatrix = getComputedStyle(carouselWrapper).transform;
                let currentTransformX = 0;
                if (currentTransformMatrix && currentTransformMatrix !== 'none') {
                    const matrixValues = currentTransformMatrix.split('(')[1].split(')')[0].split(',');
                    currentTransformX = parseFloat(matrixValues[4]); // Index 4 untuk translateX dalam matriks 2D
                }

                // Hitung nilai transformX target
                const targetTransformX = currentTransformX - shiftNeeded;

                carouselWrapper.style.transform = `translateX(${targetTransformX}px)`;

                // Jika tidak mulus (untuk lompatan instan), paksa reflow setelah mengatur transform
                if (!smooth) {
                    void carouselWrapper.offsetWidth; // Memicu reflow
                }
            }

            // Handler untuk event transitionend, digunakan untuk lompatan mulus
            function handleTransitionEnd() {
                if (currentIndex === 0) { // Jika sampai di kloning terakhir (paling kiri)
                    currentIndex = originalItemCount; // Lompat ke item asli terakhir
                    updateCarouselDisplay(false); // Lompat instan (tanpa transisi)
                } else if (currentIndex === originalItemCount + 1) { // Jika sampai di kloning pertama (paling kanan)
                    currentIndex = 1; // Lompat ke item asli pertama
                    updateCarouselDisplay(false); // Lompat instan (tanpa transisi)
                }
            }

            carouselWrapper.addEventListener('transitionend', handleTransitionEnd);

            // Event listener untuk panah kiri
            leftArrow.addEventListener('click', function() {
                currentIndex--;
                updateCarouselDisplay();
            });

            // Event listener untuk panah kanan
            rightArrow.addEventListener('click', function() {
                currentIndex++;
                updateCarouselDisplay();
            });

            // Tampilan awal (langsung ke item asli pertama tanpa transisi)
            updateCarouselDisplay(false);

            // Sesuaikan carousel saat ukuran jendela berubah
            window.addEventListener('resize', function() {
                updateCarouselDisplay(false); // Tidak ada transisi saat resize
            });
        });