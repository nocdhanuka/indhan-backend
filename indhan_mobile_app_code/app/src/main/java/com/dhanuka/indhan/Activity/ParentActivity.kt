package com.dhanuka.indhan.Activity

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.location.LocationManager
import android.net.Uri
import android.provider.Settings
import android.view.View
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import com.dhanuka.indhan.analytics.FAEvent
import com.dhanuka.indhan.analytics.FATracker
import com.dhanuka.indhan.analytics.FirebaseAnalyticsUtil.TrackableScreen
import com.dhanuka.indhan.analytics.Tracker

open class ParentActivity() : AppCompatActivity(), TrackableScreen {

    interface PermissionListener {
        fun onPermissionStatus(status: Int)
    }

    private var permissionListener: PermissionListener? = null
    private val mTracker: Tracker<FAEvent> = FATracker()
    fun checkPermission(permissionList: Array<String>, permissionListener: PermissionListener?) {
        if (permissionListener != null)
            this.permissionListener = permissionListener

        val missingPermission: ArrayList<String> = ArrayList()
        for (permission in permissionList) {
            val result: Int = ContextCompat.checkSelfPermission(this, permission)
            if (result != PackageManager.PERMISSION_GRANTED)
                missingPermission.add(permission)
        }
        if (missingPermission.isNotEmpty()) ActivityCompat.requestPermissions(
            this,
            missingPermission.toTypedArray(),
            Constants.REQUEST_CODE_PERMISSION
        )
        else
            this.permissionListener?.onPermissionStatus(Constants.PERMISSION_GRANTED)
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        when (requestCode) {
            Constants.REQUEST_CODE_PERMISSION -> {
                var permissionGranted = true
                if (grantResults.isNotEmpty()) {
                    for (result in grantResults) {
                        if (result != PackageManager.PERMISSION_GRANTED) {
                            permissionGranted = false
                            break
                        }
                    }
                }
                if (!permissionGranted) {
                    for (i in permissions.indices) {
                        if (ActivityCompat.shouldShowRequestPermissionRationale(
                                this,
                                permissions[i]
                            )
                        ) {
                            permissionListener?.onPermissionStatus(Constants.PERMISSION_DENIED)
                            break
                        } else {
                            if (ActivityCompat.checkSelfPermission(
                                    this,
                                    permissions[i]
                                ) == PackageManager.PERMISSION_GRANTED
                            ) {
                                if (i == permissions.size - 1)
                                    permissionListener?.onPermissionStatus(Constants.PERMISSION_GRANTED)
                            } else {
                                permissionListener?.onPermissionStatus(Constants.PERMISSION_SET_NEVER_AGAIN)
                                break
                            }
                        }
                    }
                } else
                    permissionListener?.onPermissionStatus(Constants.PERMISSION_GRANTED)
            }
        }
    }

    private fun isLocationEnabled(): Boolean {
        val locationManager: LocationManager =
            getSystemService(Context.LOCATION_SERVICE) as LocationManager
        return locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) || locationManager.isProviderEnabled(
            LocationManager.NETWORK_PROVIDER
        )
    }

    fun showPermissionSettingDialog() {
        AlertDialogBuilder().showDefaultDialog(this,
            "Permission Denied",
            "Please enable location permission from settings",
            "Go To Setting",
            "Cancel",
            View.VISIBLE,
            false,
            object : DialogButtonListener {
                override fun onPositiveButton() {
                    val intent = Intent(
                        Settings.ACTION_APPLICATION_DETAILS_SETTINGS,
                        Uri.fromParts("package", packageName, null)
                    )
                    intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                    startActivity(intent)
                }

                override fun onNegativeButton() {
                }
            })
    }

    open fun getTracker(): Tracker<FAEvent> {
        return mTracker
    }

    interface DialogButtonListener {
        fun onPositiveButton()
        fun onNegativeButton()
    }

    override fun getTrackingActivity(): Activity? {
        return this
    }

}