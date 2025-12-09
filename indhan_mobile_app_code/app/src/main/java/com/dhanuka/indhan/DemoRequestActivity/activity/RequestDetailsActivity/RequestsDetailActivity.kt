package com.dhanuka.indhan.DemoRequestActivity.activity.RequestDetailsActivity

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.ImageView
import androidx.appcompat.app.AppCompatActivity
import com.bumptech.glide.Glide
import com.bumptech.glide.load.engine.DiskCacheStrategy
import com.bumptech.glide.request.RequestOptions
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestData
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_requests_detail.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class RequestsDetailActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_requests_detail)
        //get data
        getDataForShowing()

    }

    /*
    Function for get data
     */
    private fun getDataForShowing() {
        if (intent.hasExtra(Constants.SEND_DEMO_REQUEST_DATA)) {
            val data: DemoRequestData =
                intent.extras?.get(Constants.SEND_DEMO_REQUEST_DATA) as DemoRequestData

            txtDemoNo.text = "Demo no: " + data.demo_code
            txtCropName.text = data.crop
            txtProductName.text = data.product
            txtFarmerName.text = data.farmer_name
            txtState.text = data.state
            txtDistrictName.text = data.district
            txtTahsilName.text = data.tehsil
            txtVillageName.text = data.village
            txtStatus.text = "Status-" + data.status

            txtMobileNo.text = data.contact_number
            txtCreatedDate.text = getTime(data.created_at)
            if (data.farmer_type.equals("new")) {
                txtFarmerType.text = "New"
            } else {
                txtFarmerType.text = "ACP farmer"

            }

            if (!data.demoImage.isNullOrBlank()) {
                Glide.with(this@RequestsDetailActivity)
                    .load(data.demoImage)
                    .into(imgDemo)
            } else {
                imgDemo.visibility = View.GONE
            }
            if (!data.farmer_image.isNullOrBlank()) {
                Glide.with(this@RequestsDetailActivity)
                    .load(data.farmer_image)
                    .into(imgDemo)
            }

            imgDemo.setOnClickListener {
                if (!data.demoImage.isNullOrBlank()) {
                    showdialogone(data.demoImage)
                }

            }

        }


    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
        try {
            date1 = sdf.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }

        val timeStr: String
        val inputFormat = SimpleDateFormat("dd-MMM-yyyy  HH:mm a")
        timeStr = inputFormat.format(date1)
        return timeStr
    }


    private fun showdialogone(url: String) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_image)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val msg = dialog.findViewById<View>(R.id.imgView) as ImageView
        var requestOptions = RequestOptions()
            .diskCacheStrategy(DiskCacheStrategy.NONE) // because file name is always same
            .skipMemoryCache(true)

        Glide.with(this)
            .load(url)
            .apply(requestOptions)
//                    .centerCrop()
//                    .crossFade()
            .into(msg)

//        msg.text = "Testimonial Successfully Uploaded"


        dialog.show()
    }
}
