<!DOCTYPE html>
<html>
<head>
  <style>
    .chart-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 20px;
    }
    
    #tempChart, #humChart {
      flex: 1;
      min-width: 300px;
      height: 400px;
    }

    .chart-title {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      font-size: 18px;
      font-weight: 500;
      margin-bottom: 10px;
      padding-left: 10px;
    }
  </style>
</head>
<body>
  <div class="chart-container">
    <div>
      <div class="chart-title">Temperature</div>
      <div id="tempChart"></div>
    </div>
    <div>
      <div class="chart-title">Humidity</div>
      <div id="humChart"></div>
    </div>
  </div>

  <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
  <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

  <script>
    am5.ready(function() {
      // Función para crear gráfico
      function createChart(root, data, title, valueField, min, max, unit) {
        var chart = root.container.children.push(
          am5xy.XYChart.new(root, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true
          })
        );

        // Crear ejes
        var xAxis = chart.xAxes.push(
          am5xy.DateAxis.new(root, {
            baseInterval: { timeUnit: "minute", count: 1 },
            renderer: am5xy.AxisRendererX.new(root, {})
          })
        );

        var yAxis = chart.yAxes.push(
          am5xy.ValueAxis.new(root, {
            min: min,
            max: max,
            renderer: am5xy.AxisRendererY.new(root, {})
          })
        );

        // Crear serie
        var series = chart.series.push(
          am5xy.LineSeries.new(root, {
            name: title,
            xAxis: xAxis,
            yAxis: yAxis,
            valueYField: "value",
            valueXField: "date",
            tooltip: am5.Tooltip.new(root, {
              labelText: `{valueY}${unit}`
            })
          })
        );

        // Estilo de la serie
        series.fills.template.setAll({
          fillOpacity: 0.3,
          visible: true
        });

        series.strokes.template.setAll({
          strokeWidth: 2
        });

        // Añadir bullets
        series.bullets.push(function() {
          return am5.Bullet.new(root, {
            sprite: am5.Circle.new(root, {
              radius: 4,
              fill: series.get("fill"),
              stroke: root.interfaceColors.get("background"),
              strokeWidth: 2
            })
          });
        });

        // Añadir cursor
        chart.set("cursor", am5xy.XYCursor.new(root, {
          xAxis: xAxis,
          behavior: "zoomX"
        }));

        // Añadir scrollbar
        chart.set("scrollbarX", am5.Scrollbar.new(root, {
          orientation: "horizontal"
        }));

        // Establecer datos
        series.data.setAll(data);

        // Animar aparición
        chart.appear(1000, 100);
      }

      <?php
      include_once 'conexion.php';
      $SQL = "SELECT * FROM temp_hum_amb ORDER BY fecha_registro";
      $consulta = mysqli_query($con, $SQL);
      
      $datos_temp = array();
      $datos_hum = array();
      
      while ($resultado = mysqli_fetch_array($consulta)) {
          $fecha = $resultado['fecha_registro'];
          
          $datos_temp[] = array(
              "date" => $fecha,
              "value" => floatval($resultado['temperatura_ambiente'])
          );
          
          $datos_hum[] = array(
              "date" => $fecha,
              "value" => floatval($resultado['humedad_ambiente'])
          );
      }
      ?>

      // Crear roots
      var tempRoot = am5.Root.new("tempChart");
      var humRoot = am5.Root.new("humChart");

      // Aplicar tema animado
      tempRoot.setThemes([am5themes_Animated.new(tempRoot)]);
      humRoot.setThemes([am5themes_Animated.new(humRoot)]);

      // Crear gráficos
      createChart(tempRoot, <?php echo json_encode($datos_temp); ?>, "Temperatura", "temperatura", 15, 25, "°C");
      createChart(humRoot, <?php echo json_encode($datos_hum); ?>, "Humedad", "humedad", 0, 100, "%");
    });
  </script>
</body>
</html>