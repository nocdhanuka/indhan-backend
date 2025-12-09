package com.dhanuka.indhan.CameraActivity;

import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.view.View;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;

import com.cjt2325.cameralibrary.JCameraView;
import com.cjt2325.cameralibrary.listener.ErrorListener;
import com.cjt2325.cameralibrary.listener.JCameraListener;
import com.dhanuka.indhan.R;

import java.io.File;
import java.io.FileOutputStream;

/**
 * Created by Johny on 10/31/2017.
 */
public class CameraActivity extends AppCompatActivity implements JCameraListener {
    JCameraView jCameraView;
    String path;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
        path = getIntent().getStringExtra("PhotoPath");
        View decorView = getWindow().getDecorView();
        if (Build.VERSION.SDK_INT >= 19) {
            decorView.setSystemUiVisibility(
                    View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                            | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                            | View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                            | View.SYSTEM_UI_FLAG_FULLSCREEN
                            | View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY);
        } else {
            int option = View.SYSTEM_UI_FLAG_FULLSCREEN;
            decorView.setSystemUiVisibility(option);
        }
        setContentView(R.layout.activity_camera_view);
        jCameraView = findViewById(R.id.jcameraview);
        jCameraView.setSaveVideoPath(Environment.getExternalStorageDirectory().getPath() + File.separator + "JCamera");
        jCameraView.setFeatures(JCameraView.BUTTON_STATE_ONLY_CAPTURE);
        jCameraView.setMediaQuality(JCameraView.MEDIA_QUALITY_MIDDLE);
        jCameraView.setJCameraLisenter(this);
        jCameraView.setErrorLisenter(new ErrorListener() {
            @Override
            public void onError() {

            }

            @Override
            public void AudioPermissionError() {

            }
        });
    }

    @Override
    public void captureSuccess(Bitmap bitmap) {
        String filePath = saveImage(bitmap);
//        String filePath = SiliCompressor.with(this).compress(i, getTempName(), false
//        );
        File file = new File(filePath);
        Intent intent = new Intent();
        intent.setData(Uri.fromFile(file));
        setResult(101, intent);
        finish();
    }

    @Override
    public void recordSuccess(String url, Bitmap firstFrame) {

    }

    @Override
    protected void onResume() {
        super.onResume();
        jCameraView.onResume();
    }

    @Override
    protected void onPause() {
        super.onPause();
        jCameraView.onPause();
    }

    public File getTempName() {
        String destinationPath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/Indhan/";
        String image = "Snap_" + System.currentTimeMillis() + ".jpg";
        File file = new File(destinationPath);

        if (file.exists()) {
            file.delete();
            file.mkdirs();
        } else {
            file.mkdirs();
        }
        return file;
    }

    private String saveImage(Bitmap bitmap) {
        String destinationPath = Environment.getExternalStorageDirectory().getAbsolutePath() + "/Indhan/";
        String image = "Snap_" + System.currentTimeMillis() + ".jpg";
        File file = new File(destinationPath);
        if (file.exists()) {
            file.delete();
            file.mkdirs();
        } else {
            file.mkdirs();
        }

        File file2 = new File(file, image);

        try {
            FileOutputStream out = new FileOutputStream(file2);
            bitmap.compress(Bitmap.CompressFormat.JPEG, 50, out);
            out.flush();
            out.close();
        } catch (Exception e) {
            e.printStackTrace();
        }
        if (!file2.exists() || file2.getPath() == null) {
            return null;
        } else {
            // return Uri.fromFile(file2);
            return file2.toString();
        }
    }

}
