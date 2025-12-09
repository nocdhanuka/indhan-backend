package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment

import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Bundle
import android.provider.Settings
import androidx.core.app.ActivityCompat
import androidx.fragment.app.Fragment
import androidx.core.content.ContextCompat
import android.view.View
import com.dhanuka.indhan.Activity.AlertDialogBuilder
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Activity.ParentActivity
import java.util.*

open class BaseFragment : Fragment() {

    private val REQUEST_CODE_PERMISSION = 199
    private var permissionListener: ParentActivity.PermissionListener? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
    }


    fun checkPermission(
        permissionList: Array<String>,
        permissionListener: ParentActivity.PermissionListener?
    ) {
        if (permissionListener != null)
            this.permissionListener = permissionListener

        val missingPermission: ArrayList<String> = ArrayList()
        for (permission in permissionList) {
            val result: Int = ContextCompat.checkSelfPermission(requireContext(), permission)
            if (result != PackageManager.PERMISSION_GRANTED)
                missingPermission.add(permission)
        }
        if (missingPermission.isNotEmpty())
            requestPermissions(
                missingPermission.toTypedArray(), REQUEST_CODE_PERMISSION
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
            REQUEST_CODE_PERMISSION -> {
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
                    for (permission in permissions) {
                        if (ActivityCompat.shouldShowRequestPermissionRationale(
                                requireActivity(),
                                permission
                            )
                        )
                            permissionListener?.onPermissionStatus(Constants.PERMISSION_DENIED)
                        else {
                            if (ActivityCompat.checkSelfPermission(
                                    requireContext(),
                                    permission
                                ) == PackageManager.PERMISSION_GRANTED
                            )
                                permissionListener?.onPermissionStatus(Constants.PERMISSION_GRANTED)
                            else
                                permissionListener?.onPermissionStatus(Constants.PERMISSION_SET_NEVER_AGAIN)
                        }
                    }
                } else
                    permissionListener?.onPermissionStatus(Constants.PERMISSION_GRANTED)
            }
        }
    }

    fun showPermissionSettingDialog() {
        AlertDialogBuilder().showDefaultDialog(requireActivity(),
            "Permission Denied",
            "Please enable location permission from settings",
            "Go To Setting",
            "Cancel",
            View.VISIBLE,
            false,
            object : ParentActivity.DialogButtonListener {
                override fun onPositiveButton() {
                    val intent = Intent(
                        Settings.ACTION_APPLICATION_DETAILS_SETTINGS,
                        Uri.fromParts("package", requireActivity().packageName, null)
                    )
                    intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                    startActivity(intent)
                }

                override fun onNegativeButton() {
                }
            })
    }
}