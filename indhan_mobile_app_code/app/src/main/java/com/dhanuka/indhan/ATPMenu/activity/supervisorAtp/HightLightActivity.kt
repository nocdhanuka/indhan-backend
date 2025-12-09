package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.TextView
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.FileUtility
import com.dhanuka.indhan.util.ImagePickerUtil
import com.dhanuka.indhan.util.ImageUtils
import com.dhanuka.indhan.util.PermissionsUtil
import kotlinx.android.synthetic.main.activity_hight_light.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import pl.aprilapps.easyphotopicker.DefaultCallback
import pl.aprilapps.easyphotopicker.EasyImage
import pl.aprilapps.easyphotopicker.MediaFile
import pl.aprilapps.easyphotopicker.MediaSource
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File

class HightLightActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }

    private var easyImage: EasyImage? = null
    private lateinit var preference: Preference
    private var ticketId = ""
    private var file: File? = null
    private lateinit var adapter: HighlightAdapter
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_hight_light)
        initCamera()
        preference = Preference(this)
        ticketId = intent?.getStringExtra("ticketId") ?: ""

        getHighLightList()

        rlAddHighlight.setOnClickListener {
            showdialog(0)
        }

    }

    private fun getHighLightList() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["ticket_id"] = ticketId

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).highlightList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<HighlightModel>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: HighlightModel) {
                    hideProgress()
                    if (listBean.statusCode == 200) {
                        if (listBean.result.size > 0) {
                            txtEmpty.visibility = View.GONE
                            adapter = HighlightAdapter(this@HightLightActivity, listBean.result)
                            recyclerView.layoutManager =
                                LinearLayoutManager(
                                    this@HightLightActivity
                                )
                            recyclerView.adapter = adapter
                        } else {
                            txtEmpty.visibility = View.VISIBLE
                        }
                    } else {

                    }
                }
            })

    }

    private fun initCamera() {
        easyImage =
            EasyImage.Builder(this@HightLightActivity)
                .setCopyImagesToPublicGalleryFolder(true)
                .setFolderName("Indhaan")
                .allowMultiple(false)
                .build()
    }

    private fun showdialog(postion: Int) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_feedback)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.edtFeedback) as EditText
        val tvOkay = dialog.findViewById<View>(R.id.btnSubmit) as Button
        val captureButton = dialog.findViewById<View>(R.id.txtCaptureImage) as TextView
        val imgView = dialog.findViewById<View>(R.id.imgView) as ImageView


        imgView.setOnClickListener {
            PermissionsUtil.askPermissions(this, PermissionsUtil.CAMERA, PermissionsUtil.STORAGE) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@HightLightActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )
                            if(file == null){
                                file = imageFile
                            }
                            imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                        },
                        ""
                    )
                }
            }
        }

        captureButton.setOnClickListener {
            PermissionsUtil.askPermissions(this, PermissionsUtil.CAMERA, PermissionsUtil.STORAGE) {
                if (it) {
                    ImagePickerUtil.pickFromCamera2(
                        easyImage,
                        this@HightLightActivity,
                        { imageFile, _ ->
                            file = ImageUtils.compressToExistingFile(
                                imageFile.absolutePath,
                                imageFile
                            )

                            if(file == null){
                                file = imageFile
                            }
                            imgView.setImageBitmap(FileUtility.createBitmapFromFile(file))
                        },
                        ""
                    )
                }
            }
        }




        tvOkay.setOnClickListener {
            var model: String = preference.getString("token")
            val query = java.util.HashMap<String, RequestBody>()
            query.put("apiToken", getRequestBody(model))
            query.put("description", getRequestBody(msg.text.toString()))
            query.put("ticket_id", getRequestBody(ticketId))

            if (file != null) {
                val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file!!)
                val imagePart1: MultipartBody.Part =
                    MultipartBody.Part.createFormData("highlightImage", file?.name, fileBody1)
                if (msg.text.toString() != "") {
                    showProgress()
                    getMarkParticepation(query, imagePart1)
                    dialog.dismiss()
                } else {
                    makeToast("Please enter comments ")

                }
            } else {
                makeToast("Please select image")
            }


        }
        dialog.show()
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)

    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        easyImage?.handleActivityResult(
            requestCode,
            resultCode,
            data,
            this,
            object : DefaultCallback() {
                override fun onMediaFilesPicked(imageFiles: Array<MediaFile>, source: MediaSource) {
                    onPhotosReturned(imageFiles);
                }
            })
    }

    private fun onPhotosReturned(imageFiles: Array<MediaFile>) {
        if (imageFiles.isNotEmpty()) {
            ImagePickerUtil.onActivityResult2(10000, RESULT_OK, imageFiles[0])
        }
    }

    private fun getMarkParticepation(
        model: HashMap<String, RequestBody>,
        part: MultipartBody.Part
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .addHighlight(model, part).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast("error")
                }

                override fun onNext(homeBean: CommonAtpResponse) {
                    hideProgress()
                    if (homeBean.status_code == 200) {
                        makeToast(homeBean.message)
                        getHighLightList()

                    } else {
                        makeToast(homeBean.message)
                    }
                }
            })
    }


    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        PermissionsUtil.onRequestPermissionsResult(requestCode, permissions, grantResults)
    }
}
