document.addEventListener("DOMContentLoaded", function() {
    const trafficLights = [
        document.getElementById("tl1"),
        document.getElementById("tl2"),
        document.getElementById("tl3"),
        document.getElementById("tl4"),
    ];

    const cars = [
        document.getElementById("car1"),
        document.getElementById("car2"),
        document.getElementById("car3"),
        document.getElementById("car4"),
    ];

    function changeLight(trafficLight, color, duration) {
        return new Promise(resolve => {
            // Apagar todas las luces
            trafficLight.querySelectorAll(".light").forEach(light => {
                light.style.opacity = 0.3;
            });

            // Encender la luz especificada
            trafficLight.querySelector(`.${color}`).style.opacity = 1;

            setTimeout(resolve, duration);
        });
    }

    function moveCar(car, distance, duration) {
        return new Promise(resolve => {
            const start = Date.now();
            function step() {
                const elapsed = Date.now() - start;
                const progress = Math.min(elapsed / duration, 1);
                car.style.left = `${progress * distance}px`;
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    resolve();
                }
            }
            step();
        });
    }

    async function trafficLightCycle() {
        while (true) {
            // Semáforos 1 y 2 en verde, 3 y 4 en rojo
            await Promise.all([
                changeLight(trafficLights[0], "green", 5000),
                changeLight(trafficLights[1], "green", 5000),
                changeLight(trafficLights[2], "red", 5000),
                changeLight(trafficLights[3], "red", 5000),
            ]);
            await Promise.all([
                moveCar(cars[0], 360, 2000),
                moveCar(cars[1], 360, 2000),
            ]);

            // Transición a amarillo
            await Promise.all([
                changeLight(trafficLights[0], "yellow", 2000),
                changeLight(trafficLights[1], "yellow", 2000),
            ]);

            // Semáforos 3 y 4 en verde, 1 y 2 en rojo
            await Promise.all([
                changeLight(trafficLights[2], "green", 5000),
                changeLight(trafficLights[3], "green", 5000),
                changeLight(trafficLights[0], "red", 5000),
                changeLight(trafficLights[1], "red", 5000),
            ]);
            await Promise.all([
                moveCar(cars[2], 360, 2000),
                moveCar(cars[3], 360, 2000),
            ]);

            // Transición a amarillo
            await Promise.all([
                changeLight(trafficLights[2], "yellow", 2000),
                changeLight(trafficLights[3], "yellow", 2000),
            ]);
        }
    }

    trafficLightCycle();
});