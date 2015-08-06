package com.sgeer.encuestas;

import android.app.Activity;
import android.content.Context;
import android.location.Criteria;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Bundle;
import android.util.Log;
import android.webkit.JavascriptInterface;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.Toast;
import java.io.FileOutputStream;
import java.io.OutputStreamWriter;


public class MainActivity extends Activity{
    @Override
    public void onCreate(Bundle savedInstanceState){
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        LocationManager milocManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        LocationListener milocListener = new MiLocationListener();
        milocManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 1000, 0, milocListener);

        WebView webview = (WebView)findViewById(R.id.webView);
        webview.setWebChromeClient(new WebChromeClient());

        WebSettings webSettings = webview.getSettings();
        webSettings.setJavaScriptEnabled(true);

        webview.addJavascriptInterface(new WebAppInterface(this), "Android");
        webview.loadUrl("file:///android_asset/encuesta.html");
    }

    public class WebAppInterface {
        Context mContext;

        WebAppInterface(Context c) {
            mContext = c;
        }

        @JavascriptInterface
        public void guardar(String texto) {

            String[] fragmento = texto.split(";");

            String usuario = fragmento[1];

            String coordenadas = "";

            IngresaRespuestas(texto + '\n', usuario);

            String bestProvider;

            LocationManager lm = (LocationManager)getSystemService(Context.LOCATION_SERVICE);
            Criteria criteria = new Criteria();
            bestProvider = lm.getBestProvider(criteria,false);

            Location location = lm.getLastKnownLocation(bestProvider);

            if(location == null){
                    Toast.makeText(getApplicationContext(), "Guardando respuesta",Toast.LENGTH_LONG ).show();
                    coordenadas = ";0,0";

            }else{
                    Toast.makeText( getApplicationContext(),"Guardando posicion y respuesta", Toast.LENGTH_LONG ).show();
                    location.getLatitude();
                    location.getLongitude();
                    coordenadas = ";" + location.getLatitude()+ "," + location.getLongitude();
            }
            IngresaCoordenadas(coordenadas, usuario);
        }
    }

    private void IngresaRespuestas(String respuestas, String usuario){

        String nombre_archivo = usuario + "_bocaurna.txt";

        try {
            FileOutputStream fOut = new FileOutputStream("/sdcard/Download/" + nombre_archivo,true);
            OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
            myOutWriter.append(respuestas);
            myOutWriter.close();
            fOut.close();

        } catch (Exception e) {
            Log.e("logGPSData", "Error");
        }
    }


    private void IngresaCoordenadas(String respuestas, String usuario){

        String nombre_archivo = usuario + "_coordenadas.xml";

        try {
            FileOutputStream fOut = new FileOutputStream("/sdcard/Download/" + nombre_archivo,true);
            OutputStreamWriter myOutWriter = new OutputStreamWriter(fOut);
            myOutWriter.append(respuestas);
            myOutWriter.close();
            fOut.close();

        } catch (Exception e) {
            Log.e("logGPSData", "Error");
        }
    }

    public class MiLocationListener implements LocationListener{
        public void onLocationChanged(Location loc){
            loc.getLatitude();
            loc.getLongitude();
            String coordenadas = ";" + loc.getLatitude()+ "," + loc.getLongitude();
        }

        public void onProviderDisabled(String provider){
            Toast.makeText( getApplicationContext(),"Active GPS por favor !", Toast.LENGTH_LONG ).show();
        }

        public void onProviderEnabled(String provider){
            Toast.makeText( getApplicationContext(),"Gps Activo", Toast.LENGTH_SHORT ).show();
        }
        public void onStatusChanged(String provider, int status, Bundle extras){}
    }
}