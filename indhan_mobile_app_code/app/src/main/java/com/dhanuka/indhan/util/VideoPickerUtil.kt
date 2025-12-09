package com.dhanuka.indhan.util

import android.app.Activity
import android.content.ContentUris
import android.content.Context
import android.content.Intent
import android.database.Cursor
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.provider.DocumentsContract
import android.provider.MediaStore
import androidx.annotation.RequiresApi
import androidx.appcompat.app.AppCompatActivity
import androidx.fragment.app.Fragment
import java.io.File

/**
 * Created by Aman on 2/14/2017.
 */

class VideopickerUtil {
    private var activity: AppCompatActivity? = null
    private var frag: Fragment? = null
    private lateinit var file: File
    private var callback: VideoPickerListener

    constructor(activity: AppCompatActivity, callback: VideoPickerListener) {
        this.activity = activity
        this.callback = callback
    }

    constructor(frag: Fragment, callback: VideoPickerListener) {
        this.frag = frag
        this.callback = callback
    }

    private fun getContext(): Context = frag?.context ?: checkNotNull(activity).baseContext

    private fun getActivity(): Activity? = frag?.activity ?: activity

    fun selectVideo(context: Context, tag: String) {
        //pickFromCamera()
        val items = arrayOf<CharSequence>("Camera", "Choose from Gallery")
        val builder = androidx.appcompat.app.AlertDialog.Builder(context)
        builder.setTitle(null)
        builder.setItems(items) { dialog, item ->
            if (items[item] == "Camera") {
                pickFromCamera()
            } else if (items[item] == "Choose from Gallery") {
                pickFromGallery()
            }
        }
        builder.show()
    }

    fun pickFromCamera() {
        val takeVideoIntent = Intent(MediaStore.ACTION_VIDEO_CAPTURE)
        takeVideoIntent.putExtra(MediaStore.EXTRA_DURATION_LIMIT, 90)
        takeVideoIntent.putExtra(MediaStore.EXTRA_VIDEO_QUALITY, 0)
        if (frag != null) {
            frag?.startActivityForResult(takeVideoIntent, 3000)
        } else if (activity != null) {
            activity?.startActivityForResult(takeVideoIntent, 3000)
        }
    }

    fun pickFromGallery() {
        val intent = Intent()
        intent.type = "video/*"
        intent.action = Intent.ACTION_GET_CONTENT//
        if (frag != null) {
            frag?.startActivityForResult(Intent.createChooser(intent, "Select File"), 4000)
        } else if (activity != null) {
            activity?.startActivityForResult(Intent.createChooser(intent, "Select File"), 4000)
        }
    }

    @RequiresApi(Build.VERSION_CODES.KITKAT)
    fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        if (resultCode == Activity.RESULT_OK) {
            when (requestCode) {
                3000 -> {
                    if (data != null) {
                        try {
                            val filePath = getPath(getContext(), data.data!!)
                            filePath?.let { callback.onVideoPicked(it, "filepath") }
                        } catch (e: Exception) {
                            e.printStackTrace()
                        }
                    }
                }
                4000 -> {
                    if (data != null) {
                        try {
                            val filePath = getPath(getContext(), data.data!!)
                            //val fileSizeInMB = videoSize(getContext(), filePath!!)
                            if (filePath?.endsWith("mp4")!!) {
                                filePath.let { callback.onVideoPicked(it, "filepath") }
                            } else {
                                callback.onError("Unsupported video format")
                            }
                            //filePath?.let { callback.onVideoPicked(it,"filepath") }
                        } catch (e: Exception) {
                            callback.onError("Unsupported video format")
                            e.printStackTrace()
                        }
                    }
                }
                5000 -> {
                    val s = data?.getStringExtra("videoUri")
                    val videoUri = Uri.parse(s)
                    val videoPath = videoUri.path
                    videoPath?.let { callback.onVideoPicked(videoUri.path!!, "filepath") }
                }
            }
        } else {
            callback.onError("Error selecting image.")
        }
    }

    @RequiresApi(Build.VERSION_CODES.KITKAT)
    fun getPath(context: Context, uri: Uri): String? {

        val isKitKat = Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT

        // DocumentProvider
        if (isKitKat && DocumentsContract.isDocumentUri(context, uri)) {
            // ExternalStorageProvider
            if (isExternalStorageDocument(uri)) {
                val docId = DocumentsContract.getDocumentId(uri)
                val split = docId.split(":".toRegex()).dropLastWhile { it.isEmpty() }.toTypedArray()
                val type = split[0]

                if ("primary".equals(type, ignoreCase = true)) {
                    return Environment.getExternalStorageDirectory().toString() + "/" + split[1]
                }

            } else if (isDownloadsDocument(uri)) {

                val id = DocumentsContract.getDocumentId(uri)
                val contentUri = ContentUris.withAppendedId(
                    Uri.parse("content://downloads/public_downloads"), java.lang.Long.valueOf(id)
                )

                return getDataColumn(context, contentUri, null, null)
            } else if (isMediaDocument(uri)) {
                val docId = DocumentsContract.getDocumentId(uri)
                val split = docId.split(":".toRegex()).dropLastWhile { it.isEmpty() }.toTypedArray()
                val type = split[0]

                var contentUri: Uri? = null
                if ("image" == type) {
                    contentUri = MediaStore.Images.Media.EXTERNAL_CONTENT_URI
                } else if ("video" == type) {
                    contentUri = MediaStore.Video.Media.EXTERNAL_CONTENT_URI
                } else if ("audio" == type) {
                    contentUri = MediaStore.Audio.Media.EXTERNAL_CONTENT_URI
                }

                val selection = "_id=?"
                val selectionArgs = arrayOf(split[1])

                return getDataColumn(context, contentUri, selection, selectionArgs)
            }// MediaProvider
            // DownloadsProvider
        } else if ("content".equals(uri.scheme!!, ignoreCase = true)) {

            // Return the remote address
            return if (isGooglePhotosUri(uri)) uri.lastPathSegment else getDataColumn(
                context,
                uri,
                null,
                null
            )

        } else if ("file".equals(uri.scheme!!, ignoreCase = true)) {
            return uri.path
        }// File
        // MediaStore (and general)

        return null
    }

    fun getDataColumn(
        context: Context,
        uri: Uri?,
        selection: String?,
        selectionArgs: Array<String>?
    ): String? {

        var cursor: Cursor? = null
        val column = "_data"
        val projection = arrayOf(column)

        try {
            cursor =
                context.contentResolver.query(uri!!, projection, selection, selectionArgs, null)
            if (cursor != null && cursor.moveToFirst()) {
                val index = cursor.getColumnIndexOrThrow(column)
                return cursor.getString(index)
            }
        } finally {
            cursor?.close()
        }
        return null
    }

    fun isExternalStorageDocument(uri: Uri): Boolean {
        return "com.android.externalstorage.documents" == uri.authority
    }

    fun isDownloadsDocument(uri: Uri): Boolean {
        return "com.android.providers.downloads.documents" == uri.authority
    }

    fun isMediaDocument(uri: Uri): Boolean {
        return "com.android.providers.media.documents" == uri.authority
    }

    fun isGooglePhotosUri(uri: Uri): Boolean {
        return "com.google.android.apps.photos.content" == uri.authority
    }
}

interface VideoPickerListener {
    fun onVideoPicked(path: String, tag: String)
    fun onError(msg: String)
}
