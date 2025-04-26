# Instrucciones de Instalación del Tema VIAWEB para Dolibarr

Este paquete contiene el módulo de tema visual para Dolibarr con la identidad de VIAWEB S.A.S.

## Requisitos Previos

- Dolibarr 13.0.0 o superior
- Permisos de administrador en Dolibarr
- Acceso al sistema de archivos del servidor

## Instalación

La instalación es extremadamente sencilla y no requiere configuración adicional.

1. **Descomprimir el módulo**
   - Descomprima el archivo `mod_viaweb_theme.zip`
   - Copie la carpeta `mod_viaweb_theme` al directorio de módulos personalizados de Dolibarr:
     ```
     /ruta/a/dolibarr/htdocs/custom/mod_viaweb_theme
     ```

2. **Activar el módulo**
   - Acceda a Dolibarr como administrador
   - Vaya a Inicio > Configuración > Módulos/Aplicaciones
   - Busque "Tema VIAWEB" en la lista de módulos
   - Active el módulo haciendo clic en el interruptor

3. **¡Listo!**
   - El tema se aplicará automáticamente al activarlo
   - No se requiere configuración adicional

## Compatibilidad

- Este tema está diseñado para funcionar perfectamente con el módulo TrelloGestiona
- Aplica un estilo coherente a toda la interfaz de Dolibarr
- Es compatible tanto con el modo claro como con el modo oscuro de Dolibarr

## Soporte

Para cualquier consulta o soporte, contacte a:

VIAWEB S.A.S
https://web.viaweb.net.ar/

## Notas importantes para entorno Wiroos/cPanel

Si está utilizando este módulo en un entorno Wiroos/cPanel:

1. Asegúrese de que los permisos de archivos sean correctos:
   ```
   chmod -R 755 mod_viaweb_theme
   chmod -R 644 mod_viaweb_theme/*.php mod_viaweb_theme/*.css mod_viaweb_theme/*.js mod_viaweb_theme/*.md
   ```

2. El tema funcionará sin necesidad de modificar ninguna configuración del servidor.

3. Si utiliza un dominio personalizado o una ruta diferente para su instalación de Dolibarr, el tema se adaptará automáticamente.

4. Este módulo no requiere base de datos ni configuración adicional - simplemente "instalar y usar".

5. Compatible con la instalación actual de TrelloGestiona.

## Licencia

Este módulo está licenciado bajo GNU/GPL v3.