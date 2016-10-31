package com.sgeer.encuestas;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.res.Configuration;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.media.MediaPlayer;
import android.os.Bundle;
import android.os.Vibrator;
import android.util.Log;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.Toast;

import java.io.FileOutputStream;
import java.io.OutputStreamWriter;

public class MainActivity extends Activity {


    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);


        LocationManager milocManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        LocationListener milocListener = new MiLocationListener();

        milocManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 1000, 0, milocListener);

        WebView webview = (WebView) findViewById(R.id.webView);
        webview.setWebChromeClient(new WebChromeClient());

        WebSettings webSettings = webview.getSettings();

        webSettings.setJavaScriptEnabled(true);

        webview.addJavascriptInterface(new WebAppInterface(this), "Android");
        webview.loadUrl("file:///android_asset/encuesta.html");

    }

    @Override
    public void onConfigurationChanged(Configuration newConfig){
        super.onConfigurationChanged(newConfig);
    }

    public class WebAppInterface {
        Context mContext;
        String coordenadas = "";

        WebAppInterface(Context c) {
            mContext = c;
        }

        @JavascriptInterface
        public void guardar(String texto) {

            String string   = texto;
            String[] parts  = string.split(";");
            String usuario  = parts[1];

            MediaPlayer mp = MediaPlayer.create(mContext, R.raw.hangouts_message);
            mp.start();
            Vibrator v = (Vibrator) mContext.getSystemService(Context.VIBRATOR_SERVICE);
            v.vibrate(400);

            String bestProvider;
            LocationManager lm = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
            Criteria criteria = new Criteria();
            bestProvider = lm.getBestProvider(criteria, false);

            Location location = lm.getLastKnownLocation(bestProvider);

            if (location == null) {

                Toast.makeText(getApplicationContext(), "Guardando respuesta sin coordenadas", Toast.LENGTH_LONG).show();
                coordenadas = "0;0;";

            } else {

                Toast.makeText(getApplicationContext(), "Guardando posicion y respuesta", Toast.LENGTH_LONG).show();

                location.getLatitude();
                location.getLongitude();
                coordenadas = location.getLatitude() + ";" + location.getLongitude() + ";";
            }

            String cadena = coordenadas + texto + '\n';

            IngresaRespuestas(cadena, usuario);
        }


        @JavascriptInterface
        public void salir() {

            Vibrator v = (Vibrator) mContext.getSystemService(Context.VIBRATOR_SERVICE);
            v.vibrate(100);
            PonerSonido();

            AlertDialog.Builder alertDialogBuilder = new AlertDialog.Builder(mContext);
            alertDialogBuilder.setTitle("Salir SGE V1.1?");

            mContext.setTheme(R.style.BotonSalir);

            alertDialogBuilder
                .setCancelable(false)
                .setPositiveButton("Si",
                    new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int id) {
                            moveTaskToBack(true);
                            android.os.Process.killProcess(android.os.Process.myPid());
                            System.exit(1);
                        }
                    })

                .setNegativeButton("No", new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int id) {

                        dialog.cancel();
                    }
                });

            AlertDialog alertDialog = alertDialogBuilder.create();
            alertDialog.show();

        }

    }

    public void PonerSonido() {
        MediaPlayer mp = MediaPlayer.create(this, R.raw.hangouts_message);
        mp.start();
    }


    private void IngresaRespuestas(String cadena, String usuario) {

        String nombre_archivo = usuario + "_respuestas.txt";

        try {
            FileOutputStream fOut = new FileOutputStream("/sdcard/Download/" + nombre_archivo, true);
            OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
            myOutWriter.append(cadena);
            myOutWriter.close();
            fOut.close();

        } catch (Exception e) {
            Log.e("logGPSData", "Error");
        }
    }


    public class MiLocationListener implements LocationListener {

        public void onLocationChanged(Location loc) {
        }

        public void onProviderDisabled(String provider) {

            Toast.makeText(getApplicationContext(), "Active GPS por favor !", Toast.LENGTH_LONG).show();
        }

        public void onProviderEnabled(String provider) {
            Toast.makeText(getApplicationContext(), "Gps Activo", Toast.LENGTH_SHORT).show();
        }

        public void onStatusChanged(String provider, int status, Bundle extras) {
        }
    }
}