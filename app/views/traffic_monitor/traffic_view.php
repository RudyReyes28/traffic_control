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
      background: #f0f0f0;
      font-family: Arial, sans-serif;
    }
    .container {
      position: relative;
      width: 800px;
      height: 800px;
      margin: 20px auto;
      background: #444;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
      border-radius: 4px;
    }
    .controls {
      width: 800px;
      margin: 10px auto;
      text-align: center;
      padding: 10px;
      background: #fff;
      border-radius: 4px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    button {
      padding: 8px 16px;
      margin: 0 5px;
      border: none;
      border-radius: 4px;
      background: #3498db;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #2980b9;
    }
    
    /* Definición de las vías */
    .road-horizontal {
      position: absolute;
      width: 100%;
      height: 200px;
      top: 300px;
      background: #555;
    }
    .road-vertical {
      position: absolute;
      width: 200px;
      height: 100%;
      left: 300px;
      background: #555;
    }
    
    /* Aceras y bordes */
    .sidewalk {
      position: absolute;
      background: #999;
    }
    .sidewalk.top {
      top: 290px;
      left: 0;
      width: 100%;
      height: 10px;
    }
    .sidewalk.bottom {
      top: 500px;
      left: 0;
      width: 100%;
      height: 10px;
    }
    .sidewalk.left {
      top: 0;
      left: 290px;
      width: 10px;
      height: 100%;
    }
    .sidewalk.right {
      top: 0;
      left: 500px;
      width: 10px;
      height: 100%;
    }
    
    /* Divisores para marcar carriles */
    .divider {
      position: absolute;
    }
    .divider.horizontal {
      top: 400px; /* centro de la vía horizontal */
      left: 0;
      width: 100%;
      height: 3px;
    }
    .divider.vertical {
      left: 400px; /* centro de la vía vertical */
      top: 0;
      width: 3px;
      height: 100%;
    }
    .divider.dashed {
      background: repeating-linear-gradient(
        90deg,
        yellow,
        yellow 15px,
        transparent 15px,
        transparent 30px
      );
    }
    .divider.dashed.vertical {
      background: repeating-linear-gradient(
        180deg,
        yellow,
        yellow 15px,
        transparent 15px,
        transparent 30px
      );
    }
    
    /* Líneas de detención */
    .stop-line {
      position: absolute;
      background: white;
    }
    .stop-line.right {
      top: 340px;
      left: 280px;
      width: 10px;
      height: 60px;
    }
    .stop-line.left {
      top: 400px;
      left: 510px;
      width: 10px;
      height: 60px;
    }
    .stop-line.down {
      top: 280px;
      left: 340px;
      width: 60px;
      height: 10px;
    }
    .stop-line.up {
      top: 510px;
      left: 400px;
      width: 60px;
      height: 10px;
    }
    
    /* Crosswalks */
    .crosswalk {
      position: absolute;
      background: repeating-linear-gradient(
        90deg,
        white,
        white 8px,
        #555 8px,
        #555 16px
      );
    }
    .crosswalk.horizontal {
      height: 20px;
      width: 80px;
    }
    .crosswalk.vertical {
      height: 80px;
      width: 20px;
      background: repeating-linear-gradient(
        0deg,
        white,
        white 8px,
        #555 8px,
        #555 16px
      );
    }
    .crosswalk.top-left {
      top: 270px;
      left: 210px;
    }
    .crosswalk.top-right {
      top: 270px;
      left: 510px;
    }
    .crosswalk.bottom-left {
      top: 510px;
      left: 210px;
    }
    .crosswalk.bottom-right {
      top: 510px;
      left: 510px;
    }
    .crosswalk.left-top {
      top: 210px;
      left: 270px;
    }
    .crosswalk.left-bottom {
      top: 510px;
      left: 270px;
    }
    .crosswalk.right-top {
      top: 210px;
      left: 510px;
    }
    .crosswalk.right-bottom {
      top: 510px;
      left: 510px;
    }
    
    /* Semáforos */
    .semaphore-post {
      position: absolute;
      width: 8px;
      height: 70px;
      background: #333;
      z-index: 100;
    }
    .semaphore-housing {
      position: absolute;
      width: 20px;
      height: 60px;
      background: #222;
      border-radius: 4px;
      display: flex;
      flex-direction: column;
      justify-content: space-evenly;
      align-items: center;
      padding: 2px;
      z-index: 100;
    }
    .semaphore-light {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: #333;
      box-shadow: inset 0 0 5px rgba(0,0,0,0.5);
    }
    .semaphore-light.red.active { background: red; box-shadow: 0 0 10px red; }
    .semaphore-light.yellow.active { background: yellow; box-shadow: 0 0 10px yellow; }
    .semaphore-light.green.active { background: green; box-shadow: 0 0 10px green; }
    
    /* Posiciones de los semáforos */
    #semaphore-right-post {
      top: 230px;
      left: 270px;
    }
    #semaphore-right-housing {
      top: 160px;
      left: 264px;
    }
    
    #semaphore-left-post {
      top: 500px;
      left: 522px;
    }
    #semaphore-left-housing {
      top: 430px;
      left: 516px;
    }
    
    #semaphore-down-post {
      top: 270px;
      left: 520px;
    }
    #semaphore-down-housing {
      top: 200px;
      left: 514px;
    }
    
    #semaphore-up-post {
      top: 500px;
      left: 270px;
    }
    #semaphore-up-housing {
      top: 430px;
      left: 264px;
    }
    
    /* Estilo para los autos */
    .car {
      position: absolute;
      width: 40px;
      height: 20px;
      border-radius: 5px;
      transition: transform 0.2s;
      box-shadow: 0 0 5px rgba(0,0,0,0.5);
      z-index: 50;
    }
    .car .blinker {
      position: absolute;
      width: 5px;
      height: 5px;
      background: orange;
      border-radius: 50%;
      opacity: 0;
    }
    .car .blinker.left {
      top: 2px;
      left: 2px;
    }
    .car .blinker.right {
      top: 2px;
      right: 2px;
    }
    .car .blinker.active {
      animation: blink 0.5s infinite;
    }
    
    @keyframes blink {
      0%, 49% { opacity: 0; }
      50%, 100% { opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Vías -->
    <div class="road-horizontal"></div>
    <div class="road-vertical"></div>
    
    <!-- Aceras -->
    <div class="sidewalk top"></div>
    <div class="sidewalk bottom"></div>
    <div class="sidewalk left"></div>
    <div class="sidewalk right"></div>
    
    <!-- Divisores de carriles -->
    <div class="divider horizontal dashed"></div>
    <div class="divider vertical dashed"></div>
    
    <!-- Líneas de detención -->
    <div class="stop-line right"></div>
    <div class="stop-line left"></div>
    <div class="stop-line down"></div>
    <div class="stop-line up"></div>
    
    <!-- Pasos de peatones -->
    <div class="crosswalk horizontal top-left"></div>
    <div class="crosswalk horizontal top-right"></div>
    <div class="crosswalk horizontal bottom-left"></div>
    <div class="crosswalk horizontal bottom-right"></div>
    <div class="crosswalk vertical left-top"></div>
    <div class="crosswalk vertical left-bottom"></div>
    <div class="crosswalk vertical right-top"></div>
    <div class="crosswalk vertical right-bottom"></div>
    
    <!-- Semáforos -->
    <!-- Semáforo para dirección derecha (ahora en la posición inferior) -->
    <div id="semaphore-right-post" class="semaphore-post"></div>
    <div id="semaphore-right-housing" class="semaphore-housing">
      <div id="semaphore-right-red" class="semaphore-light red"></div>
      <div id="semaphore-right-yellow" class="semaphore-light yellow"></div>
      <div id="semaphore-right-green" class="semaphore-light green active"></div>
    </div>
    
    <!-- Semáforo para dirección izquierda (ahora en la posición superior) -->
    <div id="semaphore-left-post" class="semaphore-post"></div>
    <div id="semaphore-left-housing" class="semaphore-housing">
      <div id="semaphore-left-red" class="semaphore-light red"></div>
      <div id="semaphore-left-yellow" class="semaphore-light yellow"></div>
      <div id="semaphore-left-green" class="semaphore-light green active"></div>
    </div>
    
    <!-- Semáforo para dirección abajo -->
    <div id="semaphore-down-post" class="semaphore-post"></div>
    <div id="semaphore-down-housing" class="semaphore-housing">
      <div id="semaphore-down-red" class="semaphore-light red active"></div>
      <div id="semaphore-down-yellow" class="semaphore-light yellow"></div>
      <div id="semaphore-down-green" class="semaphore-light green"></div>
    </div>
    
    <!-- Semáforo para dirección arriba -->
    <div id="semaphore-up-post" class="semaphore-post"></div>
    <div id="semaphore-up-housing" class="semaphore-housing">
      <div id="semaphore-up-red" class="semaphore-light red active"></div>
      <div id="semaphore-up-yellow" class="semaphore-light yellow"></div>
      <div id="semaphore-up-green" class="semaphore-light green"></div>
    </div>
  </div>
  
  <div class="controls">
    <button id="btn-increase-traffic">Aumentar Tráfico</button>
    <button id="btn-decrease-traffic">Disminuir Tráfico</button>
    <button id="btn-toggle-sim">Pausar Simulación</button>
  </div>
  
  <script>
    // Configuración de la simulación
    const container = document.querySelector('.container');
    const safeDistance = 30; // Distancia mínima entre vehículos
    const baseSpeed = 2;     // Velocidad base en píxeles por frame
    const decelerationRate = 0.3; // Tasa de desaceleración
    const accelerationRate = 0.1; // Tasa de aceleración
    
    // Variables de control de la simulación
    let isPaused = false;
    let spawnRate = {
      right: 3000,
      left: 3000,
      down: 4000,
      up: 4000
    };
    
    // Estados del semáforo
    const RED = 'red';
    const YELLOW = 'yellow';
    const GREEN = 'green';
    
    // Fases de semáforo con tiempos para cada estado
    const phases = {
      horizontal: {
        duration: 5000,
        states: {
          right: GREEN,
          left: GREEN,
          down: RED,
          up: RED
        }
      },
      yellowToVertical: {
        duration: 1500,
        states: {
          right: YELLOW,
          left: YELLOW,
          down: RED,
          up: RED
        }
      },
      vertical: {
        duration: 5000,
        states: {
          right: RED,
          left: RED,
          down: GREEN,
          up: GREEN
        }
      },
      yellowToHorizontal: {
        duration: 1500,
        states: {
          right: RED,
          left: RED,
          down: YELLOW,
          up: YELLOW
        }
      }
    };

    let currentPhaseIndex = 0;
const phaseSequence = ['horizontal', 'yellowToVertical', 'vertical', 'yellowToHorizontal'];
let currentPhase = phaseSequence[currentPhaseIndex];

const semaphores = {
  right: {
    red: document.getElementById('semaphore-right-red'),
    yellow: document.getElementById('semaphore-right-yellow'),
    green: document.getElementById('semaphore-right-green')
  },
  left: {
    red: document.getElementById('semaphore-left-red'),
    yellow: document.getElementById('semaphore-left-yellow'),
    green: document.getElementById('semaphore-left-green')
  },
  down: {
    red: document.getElementById('semaphore-down-red'),
    yellow: document.getElementById('semaphore-down-yellow'),
    green: document.getElementById('semaphore-down-green')
  },
  up: {
    red: document.getElementById('semaphore-up-red'),
    yellow: document.getElementById('semaphore-up-yellow'),
    green: document.getElementById('semaphore-up-green')
  }
};

// Límites de la intersección
const intersection = {
  left: 290,
  right: 510,
  top: 290,
  bottom: 510
};

// Función para actualizar los semáforos según la fase actual
function updateSemaphores() {
  const phaseConfig = phases[currentPhase];
  
  for (const direction in phaseConfig.states) {
    const state = phaseConfig.states[direction];
    
    // Resetear todas las luces
    semaphores[direction].red.classList.remove('active');
    semaphores[direction].yellow.classList.remove('active');
    semaphores[direction].green.classList.remove('active');
    
    // Activar la luz correspondiente
    semaphores[direction][state].classList.add('active');
  }
}

// Cambiar fase del semáforo secuencialmente
function advancePhase() {
  currentPhaseIndex = (currentPhaseIndex + 1) % phaseSequence.length;
  currentPhase = phaseSequence[currentPhaseIndex];
  updateSemaphores();
  
  // Programar el siguiente cambio de fase
  setTimeout(advancePhase, phases[currentPhase].duration);
}

// Iniciar ciclo de semáforos
setTimeout(advancePhase, phases[currentPhase].duration);

// Clase para representar un vehículo
class Vehicle {
  constructor(lane) {
    this.lane = lane; // "right", "left", "down", "up"
    this.maxSpeed = baseSpeed + (Math.random() * 0.5); // Velocidad máxima ligeramente aleatoria
    this.speed = this.maxSpeed;
    this.width = 40;
    this.height = 20;
    this.turning = false;
    this.turnDirection = null; // 'left', 'right', or null para no girar
    this.turnProgress = 0; // 0 a 1, donde 1 significa que completó el giro
    this.element = document.createElement('div');
    this.element.className = 'car';
    
    // Asignar probabilidad de giro (30% de probabilidad)
    if (Math.random() < 0.3) {
      this.turnDirection = Math.random() < 0.5 ? 'left' : 'right';
      
      // Crear luces intermitentes
      const leftBlinker = document.createElement('div');
      leftBlinker.className = 'blinker left';
      this.element.appendChild(leftBlinker);
      
      const rightBlinker = document.createElement('div');
      rightBlinker.className = 'blinker right';
      this.element.appendChild(rightBlinker);
      
      // Activar el intermitente correspondiente
      if (this.turnDirection === 'left') {
        leftBlinker.classList.add('active');
      } else {
        rightBlinker.classList.add('active');
      }
    }
    
    // Color aleatorio
    const hue = Math.floor(Math.random() * 360);
    this.element.style.background = `hsl(${hue}, 70%, 50%)`;
    container.appendChild(this.element);
    
    // Posición inicial y orientación según el carril
    // Cambiamos las posiciones para que left esté arriba y right abajo
    if (lane === 'right') {
      this.x = -50;
      this.y = 440; // Ajustado para estar en el carril inferior
      this.stopLine = 280;
      this.rotation = 0; // 0 grados, hacia la derecha
    } else if (lane === 'left') {
      this.x = 850;
      this.y = 340; // Ajustado para estar en el carril superior
      this.stopLine = 520;
      this.rotation = 180; // 180 grados, hacia la izquierda
    } else if (lane === 'down') {
      this.x = 340;
      this.y = -50;
      this.stopLine = 280;
      this.rotation = 90; // 90 grados, hacia abajo
      // Intercambiar ancho y alto para orientación vertical
      [this.width, this.height] = [this.height, this.width];
    } else if (lane === 'up') {
      this.x = 440;
      this.y = 850;
      this.stopLine = 520;
      this.rotation = 270; // 270 grados, hacia arriba
      // Intercambiar ancho y alto para orientación vertical
      [this.width, this.height] = [this.height, this.width];
    }
    
    this.updatePosition();
  }
  
  updatePosition() {
    this.element.style.left = `${this.x}px`;
    this.element.style.top = `${this.y}px`;
    this.element.style.transform = `rotate(${this.rotation}deg)`;
  }
  
  // Verificar si está en la intersección
  isInIntersection() {
    return (
      this.x + this.width > intersection.left &&
      this.x < intersection.right &&
      this.y + this.height > intersection.top &&
      this.y < intersection.bottom
    );
  }
  
  // Verificar si puede girar
  canTurn() {
    if (!this.turnDirection) return false;
    
    // Puntos de decisión para cada carril
    const turnPoints = {
      right: 400, // x-coordenada para girar
      left: 400,
      down: 400, // y-coordenada para girar
      up: 400
    };
    
    let inPosition = false;
    
    // Verificar si está en posición para girar
    if (this.lane === 'right' && this.x >= turnPoints.right) inPosition = true;
    if (this.lane === 'left' && this.x <= turnPoints.left) inPosition = true;
    if (this.lane === 'down' && this.y >= turnPoints.down) inPosition = true;
    if (this.lane === 'up' && this.y <= turnPoints.up) inPosition = true;
    
    // Solo puede girar si está en la intersección y el semáforo está en verde
    return (
      inPosition && 
      this.isInIntersection() && 
      phases[currentPhase].states[this.lane] === GREEN
    );
  }
  
  // Realizar giro
  turn() {
    if (!this.turning && this.canTurn()) {
      this.turning = true;
    }
    
    if (this.turning) {
      this.turnProgress += 0.03; // Velocidad de giro
      
      if (this.turnProgress >= 1) {
        // Giro completado
        this.turnProgress = 1;
        this.turning = false;
        
        // Cambiar carril según el giro
        // Ajustado para las nuevas posiciones de los carriles
        if (this.lane === 'right') {
          if (this.turnDirection === 'left') {
            this.lane = 'up';
            this.rotation = 270;
          } else {
            this.lane = 'down';
            this.rotation = 90;
          }
        } else if (this.lane === 'left') {
          if (this.turnDirection === 'left') {
            this.lane = 'down';
            this.rotation = 90;
          } else {
            this.lane = 'up';
            this.rotation = 270;
          }
        } else if (this.lane === 'down') {
          if (this.turnDirection === 'left') {
            this.lane = 'left';  // Ajustado: ahora gira hacia el carril superior
            this.rotation = 180;
          } else {
            this.lane = 'right'; // Ajustado: ahora gira hacia el carril inferior
            this.rotation = 0;
          }
        } else if (this.lane === 'up') {
          if (this.turnDirection === 'left') {
            this.lane = 'right'; // Ajustado: ahora gira hacia el carril inferior
            this.rotation = 0;
          } else {
            this.lane = 'left';  // Ajustado: ahora gira hacia el carril superior
            this.rotation = 180;
          }
        }
        
        // Intercambiar ancho y alto si es necesario
        if (this.lane === 'up' || this.lane === 'down') {
          if (this.width > this.height) {
            [this.width, this.height] = [this.height, this.width];
          }
        } else {
          if (this.height > this.width) {
            [this.width, this.height] = [this.height, this.width];
          }
        }
        
        // Eliminar intermitentes después de girar
        this.turnDirection = null;
        const blinkers = this.element.querySelectorAll('.blinker');
        blinkers.forEach(blinker => blinker.classList.remove('active'));
      } else {
        // Calcular posición durante el giro
        let centerX = 400;
        let centerY = 400;
        let radius = 60;
        
        // Ajustamos el cálculo de giro para los nuevos carriles
        if (this.lane === 'right') {
          if (this.turnDirection === 'left') {
            // De carril inferior a arriba
            let angle = 270 + 90 * this.turnProgress;
            if (angle >= 360) angle -= 360;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          } else {
            // De carril inferior a abajo
            let angle = 90 * this.turnProgress;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          }
        } else if (this.lane === 'left') {
          if (this.turnDirection === 'left') {
            // De carril superior a abajo
            let angle = 180 + 90 * this.turnProgress;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          } else {
            // De carril superior a arriba
            let angle = 180 - 90 * this.turnProgress;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY - radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          }
        } else if (this.lane === 'down') {
          if (this.turnDirection === 'left') {
            // De abajo a carril superior (izquierda)
            let angle = 90 + 90 * this.turnProgress;
            this.x = centerX - radius * Math.cos((90 - angle) * Math.PI / 180);
            this.y = centerY + radius * Math.sin((90 - angle) * Math.PI / 180);
            this.rotation = angle;
          } else {
            // De abajo a carril inferior (derecha)
            let angle = 90 - 90 * this.turnProgress;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          }
        } else if (this.lane === 'up') {
          if (this.turnDirection === 'left') {
            // De arriba a carril inferior (derecha)
            let angle = 270 + 90 * this.turnProgress;
            if (angle >= 360) angle -= 360;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          } else {
            // De arriba a carril superior (izquierda)
            let angle = 270 - 90 * this.turnProgress;
            this.x = centerX + radius * Math.cos(angle * Math.PI / 180);
            this.y = centerY + radius * Math.sin(angle * Math.PI / 180);
            this.rotation = angle;
          }
        }
      }
    }
  }
  
  // Verificar si debería detenerse por el semáforo
  shouldStopForTrafficLight() {
    // Solo se detiene si el semáforo está en rojo o amarillo
    const currentLightState = phases[currentPhase].states[this.lane];
    
    if (currentLightState === RED || currentLightState === YELLOW) {
      if (this.lane === 'right') {
        return this.x + this.width < this.stopLine;
      } else if (this.lane === 'left') {
        return this.x > this.stopLine;
      } else if (this.lane === 'down') {
        return this.y + this.height < this.stopLine;
      } else if (this.lane === 'up') {
        return this.y > this.stopLine;
      }
    }
    return false;
  }
  
  // Actualizar posición y comportamiento
  update(obstacles) {
    // Si está girando, manejar el giro
    if (this.turning) {
      this.turn();
      return;
    }
    
    // Verificar si puede empezar a girar
    if (!this.turning && this.turnDirection && this.canTurn()) {
      this.turn();
      return;
    }
    
    // Por defecto, acelerar hasta la velocidad máxima
    if (this.speed < this.maxSpeed) {
      this.speed += accelerationRate;
      if (this.speed > this.maxSpeed) {
        this.speed = this.maxSpeed;
      }
    }
    
    // Verificar obstáculos adelante y el semáforo
    let shouldStop = this.shouldStopForTrafficLight();
    let minDistance = Infinity;

    obstacles.forEach(vehicle => {
  if (vehicle !== this && vehicle.lane === this.lane) {
    let distance = Infinity;
    
    if (this.lane === 'right') {
      if (vehicle.x > this.x && vehicle.x - (this.x + this.width) < minDistance) {
        distance = vehicle.x - (this.x + this.width);
      }
    } else if (this.lane === 'left') {
      if (vehicle.x < this.x && (this.x - (vehicle.x + vehicle.width)) < minDistance) {
        distance = this.x - (vehicle.x + vehicle.width);
      }
    } else if (this.lane === 'down') {
      if (vehicle.y > this.y && vehicle.y - (this.y + this.height) < minDistance) {
        distance = vehicle.y - (this.y + this.height);
      }
    } else if (this.lane === 'up') {
      if (vehicle.y < this.y && (this.y - (vehicle.y + vehicle.height)) < minDistance) {
        distance = this.y - (vehicle.y + vehicle.height);
      }
    }
    
    if (distance < minDistance) {
      minDistance = distance;
    }
  }
});

// Ajustar velocidad basado en la distancia al obstáculo más cercano
if (minDistance < safeDistance) {
  // Desacelerar gradualmente cuando se acerca a otro vehículo
  this.speed -= decelerationRate;
  if (this.speed < 0) this.speed = 0;
  shouldStop = true;
} else if (minDistance < safeDistance * 2) {
  // Mantener una velocidad reducida si está relativamente cerca de otro vehículo
  this.speed = Math.min(this.speed, this.maxSpeed * 0.5);
  shouldStop = true;
}

// Si debe detenerse por semáforo o vehículo, desacelerar gradualmente
if (shouldStop && this.speed > 0) {
  this.speed -= decelerationRate;
  if (this.speed < 0) this.speed = 0;
}

// Actualizar posición según la dirección
if (this.lane === 'right') {
  this.x += this.speed;
} else if (this.lane === 'left') {
  this.x -= this.speed;
} else if (this.lane === 'down') {
  this.y += this.speed;
} else if (this.lane === 'up') {
  this.y -= this.speed;
}

this.updatePosition();
}

// Verificar si el vehículo está fuera del área de simulación
isOutOfBounds() {
  if (this.lane === 'right' && this.x > 850) return true;
  if (this.lane === 'left' && this.x < -50) return true;
  if (this.lane === 'down' && this.y > 850) return true;
  if (this.lane === 'up' && this.y < -50) return true;
  return false;
}
}

// Arreglos para almacenar vehículos
let vehicles = [];

// Función para generar vehículos en un carril específico
function spawnVehicle(lane) {
  if (isPaused) return;
  
  let vehicle = new Vehicle(lane);
  vehicles.push(vehicle);
}

// Temporizadores para generar vehículos
let spawnTimers = {};

function startSpawnTimers() {
  spawnTimers.right = setInterval(() => spawnVehicle('right'), spawnRate.right);
  spawnTimers.left = setInterval(() => spawnVehicle('left'), spawnRate.left);
  spawnTimers.down = setInterval(() => spawnVehicle('down'), spawnRate.down);
  spawnTimers.up = setInterval(() => spawnVehicle('up'), spawnRate.up);
}

function updateSpawnTimers() {
  // Detener temporizadores existentes
  for (const timer in spawnTimers) {
    clearInterval(spawnTimers[timer]);
  }
  
  // Iniciar nuevos temporizadores con tasas actualizadas
  startSpawnTimers();
}

// Iniciar temporizadores de generación
startSpawnTimers();

// Función de animación para actualizar la simulación
function animate() {
  if (isPaused) return;
  
  // Actualizar cada vehículo
  vehicles.forEach(vehicle => {
    vehicle.update(vehicles);
  });
  
  // Eliminar vehículos que salieron del área de simulación
  vehicles = vehicles.filter(vehicle => !vehicle.isOutOfBounds());
  
  requestAnimationFrame(animate);
}

// Iniciar animación
animate();

// Controles de la interfaz
const btnIncrease = document.getElementById('btn-increase-traffic');
const btnDecrease = document.getElementById('btn-decrease-traffic');
const btnToggleSim = document.getElementById('btn-toggle-sim');

btnIncrease.addEventListener('click', () => {
  // Aumentar frecuencia de generación disminuyendo el tiempo entre vehículos
  for (const lane in spawnRate) {
    spawnRate[lane] = Math.max(spawnRate[lane] * 0.8, 500); // Límite mínimo de 500ms
  }
  updateSpawnTimers();
});

btnDecrease.addEventListener('click', () => {
  // Disminuir frecuencia de generación aumentando el tiempo entre vehículos
  for (const lane in spawnRate) {
    spawnRate[lane] = Math.min(spawnRate[lane] * 1.2, 6000); // Límite máximo de 6000ms
  }
  updateSpawnTimers();
});

btnToggleSim.addEventListener('click', () => {
  isPaused = !isPaused;
  btnToggleSim.textContent = isPaused ? 'Reanudar Simulación' : 'Pausar Simulación';
  
  if (!isPaused) {
    animate(); // Reiniciar animación si estaba pausada
  }
});

// Iniciar ciclo de semáforos
updateSemaphores();
setTimeout(advancePhase, phases[currentPhase].duration);
  </script>