<?php
// index.php (Este archivo PHP sirve la vista; en este ejemplo la lógica se implementa con JS)
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Simulación Realista de Intersección</title>
  <style>
    /* Estilos generales */
    body {
      margin: 0;
      padding: 0;
      background: #eee;
    }
    .container {
      position: relative;
      width: 800px;
      height: 800px;
      margin: 20px auto;
      background: #444;
      overflow: hidden;
    }
    /* Definición de las vías */
    .road-horizontal {
      position: absolute;
      width: 100%;
      height: 200px;
      top: 300px;
      background: #666;
    }
    .road-vertical {
      position: absolute;
      width: 200px;
      height: 100%;
      left: 300px;
      background: #666;
    }
    /* Divisores para marcar carriles */
    .divider.horizontal {
      position: absolute;
      top: 400px; /* centro de la vía horizontal (300 + 200/2) */
      left: 0;
      width: 100%;
      border-top: 2px dashed white;
    }
    .divider.vertical {
      position: absolute;
      left: 400px; /* centro de la vía vertical (300 + 200/2) */
      top: 0;
      height: 100%;
      border-left: 2px dashed white;
    }
    /* Semáforos */
    .semaphore {
      position: absolute;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: 3px solid #333;
    }
    /* Ubicación de semáforos para cada dirección */
    /* En esta implementación, se usan dos fases:
         - Fase horizontal: Los autos en "right" y "left" tienen luz verde.
         - Fase vertical: Los autos en "down" y "up" tienen luz verde.
    */
    #semaphoreRight {
      top: 260px;
      left: 260px;
    }
    #semaphoreLeft {
      top: 460px;
      right: 260px;
    }
    #semaphoreDown {
      top: 260px;
      right: 260px;
    }
    #semaphoreUp {
      bottom: 260px;
      left: 260px;
    }
    .semaphore.green { background: green; }
    .semaphore.red   { background: red; }
    /* Estilo para los autos */
    .car {
      position: absolute;
      width: 40px;
      height: 20px;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Vías -->
    <div class="road-horizontal"></div>
    <div class="road-vertical"></div>
    
    <!-- Divisores de carriles -->
    <div class="divider horizontal"></div>
    <div class="divider vertical"></div>
    
    <!-- Semáforos (se posicionan en las esquinas de la intersección) -->
    <div id="semaphoreRight" class="semaphore green"></div>
    <div id="semaphoreLeft" class="semaphore green"></div>
    <div id="semaphoreDown" class="semaphore red"></div>
    <div id="semaphoreUp" class="semaphore red"></div>
  </div>
  
  <script>
    // Configuración de la simulación
    const container = document.querySelector('.container');
    const safeDistance = 50; // Distancia mínima entre vehículos (para evitar traslapos)
    const baseSpeed = 2;     // Velocidad base en píxeles por frame

    // Fases de semáforo: solo dos fases (horizontal y vertical)
    // En "horizontal" la dirección right & left son verdes; en "vertical" down & up son verdes.
    let currentPhase = "horizontal";

    const semRightElem = document.getElementById('semaphoreRight');
    const semLeftElem  = document.getElementById('semaphoreLeft');
    const semDownElem  = document.getElementById('semaphoreDown');
    const semUpElem    = document.getElementById('semaphoreUp');

    function updateSemaphores() {
      if (currentPhase === "horizontal") {
        semRightElem.className = 'semaphore green';
        semLeftElem.className  = 'semaphore green';
        semDownElem.className  = 'semaphore red';
        semUpElem.className    = 'semaphore red';
      } else {
        semRightElem.className = 'semaphore red';
        semLeftElem.className  = 'semaphore red';
        semDownElem.className  = 'semaphore green';
        semUpElem.className    = 'semaphore green';
      }
    }

    // Alternar fase cada 5 segundos
    setInterval(() => {
      currentPhase = (currentPhase === "horizontal") ? "vertical" : "horizontal";
      updateSemaphores();
    }, 5000);

    // Clase para representar un vehículo
    class Vehicle {
      constructor(lane) {
        this.lane = lane; // "right", "left", "down", "up"
        this.speed = baseSpeed;
        this.width = 40;
        this.height = 20;
        this.element = document.createElement('div');
        this.element.className = 'car';
        // Color aleatorio
        this.element.style.background = '#' + Math.floor(Math.random() * 16777215).toString(16);
        container.appendChild(this.element);
        
        // Posición inicial según el carril
        if (lane === 'right') {
          this.x = -50;
          this.y = 340; // carril superior (centrado en 350 - 10)
          this.stopLine = 280; // Los autos se detienen si no han cruzado x = 280
        } else if (lane === 'left') {
          this.x = 850;
          this.y = 440; // carril inferior (centrado en 450 - 10)
          this.stopLine = 520; // se detiene si no ha cruzado x = 520
        } else if (lane === 'down') {
          this.x = 340;
          this.y = -50;
          this.stopLine = 280; // se detiene si no ha cruzado y = 280
        } else if (lane === 'up') {
          this.x = 440;
          this.y = 850;
          this.stopLine = 520; // se detiene si no ha cruzado y = 520
        }
        this.updatePosition();
      }
      
      updatePosition() {
        this.element.style.left = this.x + 'px';
        this.element.style.top = this.y + 'px';
      }
      
      update() {
        // Dependiendo del carril, se verifica si el vehículo aún no ha cruzado la línea de detención.
        // Si la fase actual no permite avanzar en ese carril y el vehículo aún está fuera de la intersección, se detiene.
        if (this.lane === 'right') {
          // Vehículos de izquierda a derecha
          if ( (this.x + this.width) < this.stopLine && currentPhase !== "horizontal" ) {
            this.speed = 0;
          } else {
            this.speed = baseSpeed;
          }
          this.x += this.speed;
        } else if (this.lane === 'left') {
          // Vehículos de derecha a izquierda
          if ( this.x > this.stopLine && currentPhase !== "horizontal" ) {
            this.speed = 0;
          } else {
            this.speed = baseSpeed;
          }
          this.x -= this.speed;
        } else if (this.lane === 'down') {
          // Vehículos de arriba hacia abajo
          if ( (this.y + this.height) < this.stopLine && currentPhase !== "vertical" ) {
            this.speed = 0;
          } else {
            this.speed = baseSpeed;
          }
          this.y += this.speed;
        } else if (this.lane === 'up') {
          // Vehículos de abajo hacia arriba
          if ( this.y > this.stopLine && currentPhase !== "vertical" ) {
            this.speed = 0;
          } else {
            this.speed = baseSpeed;
          }
          this.y -= this.speed;
        }
        this.updatePosition();
      }
    }

    // Arreglos para almacenar vehículos por carril
    let vehicles = {
      right: [],
      left: [],
      down: [],
      up: []
    };

    // Función para generar vehículos en un carril específico
    function spawnVehicle(lane) {
      let vehicle = new Vehicle(lane);
      vehicles[lane].push(vehicle);
    }

    // Generar vehículos periódicamente en cada carril
    setInterval(() => { spawnVehicle('right'); }, 3000);
    setInterval(() => { spawnVehicle('left'); }, 3000);
    setInterval(() => { spawnVehicle('down'); }, 4000);
    setInterval(() => { spawnVehicle('up'); }, 4000);

    // Función de animación para actualizar la simulación
    function animate() {
      // Para cada carril
      for (let lane in vehicles) {
        // Ordenar vehículos según su dirección para evitar traslapos
        if (lane === 'right') {
          vehicles[lane].sort((a, b) => a.x - b.x);
        } else if (lane === 'left') {
          vehicles[lane].sort((a, b) => b.x - a.x);
        } else if (lane === 'down') {
          vehicles[lane].sort((a, b) => a.y - b.y);
        } else if (lane === 'up') {
          vehicles[lane].sort((a, b) => b.y - a.y);
        }
        // Actualizar cada vehículo
        for (let i = 0; i < vehicles[lane].length; i++) {
          let vehicle = vehicles[lane][i];
          // Verificar distancia segura con el vehículo que está adelante en el mismo carril
          let safe = true;
          if (i > 0) {
            let vehicleAhead = vehicles[lane][i - 1];
            if (lane === 'right') {
              if (vehicleAhead.x - (vehicle.x + vehicle.width) < safeDistance) safe = false;
            } else if (lane === 'left') {
              if (vehicle.x - (vehicleAhead.x + vehicleAhead.width) < safeDistance) safe = false;
            } else if (lane === 'down') {
              if (vehicleAhead.y - (vehicle.y + vehicle.height) < safeDistance) safe = false;
            } else if (lane === 'up') {
              if (vehicle.y - (vehicleAhead.y + vehicleAhead.height) < safeDistance) safe = false;
            }
          }
          if (!safe) {
            vehicle.speed = 0;
          }
          vehicle.update();
        }
        // Eliminar vehículos que ya salieron del área de simulación
        vehicles[lane] = vehicles[lane].filter(v => {
          if (v.lane === 'right' && v.x > 850) { v.element.remove(); return false; }
          if (v.lane === 'left' && v.x < -50) { v.element.remove(); return false; }
          if (v.lane === 'down' && v.y > 850) { v.element.remove(); return false; }
          if (v.lane === 'up' && v.y < -50) { v.element.remove(); return false; }
          return true;
        });
      }
      requestAnimationFrame(animate);
    }

    animate();
  </script>
</body>
</html>
