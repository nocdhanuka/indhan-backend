package com.dhanuka.indhan.TestimonialList

import android.app.Dialog
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanATP.View.TestimonialActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Testimonial.service.ServiceConstrants
import com.dhanuka.indhan.TestimonialList.filter.TestimonialFilterActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_testimonial_list.*

class TestimonialListActivity : BaseActivity(), TestimonialListContractor.TestimonialListView {
    override fun onClick(v: View?) {

    }

    override fun setItems(items: TestimonialBean) {
        setUp(items)
    }

    override fun failure(items: String) {
        makeToast(items)
    }

    private lateinit var mPresenterImpl: TestimonialListContractor.TestimonialListPresenter
    private lateinit var preference: Preference
    private lateinit var adapter: TestimonilListAdapter
    private var testimonial = ""
    private var product = ""
    private var productId = ""
    private var startDate = ""
    private var endDate = ""
    private var cvs = ""
    private var rating = ""
    private var ratingList = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_testimonial_list)

        hideUploadingProgress()

        preference = Preference(this)

        mPresenterImpl = TestimonialListPresenterImpl(this)
        pd_name.text = "Master Testimonials"
        swipeRefreshLayout.setOnRefreshListener {
            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["testimonial_type"] = testimonial
            query["from"] = startDate
            query["to"] = endDate
            query["product"] = productId
            //query["dd_id"] = cvs
            query["rating"] = rating
            if (CheckConnectionStatus.isInternetAvailable(this@TestimonialListActivity)) {
                mPresenterImpl.onGetTestimonialList(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
            swipeRefreshLayout.isRefreshing = false

        }

        imgFilter.setOnClickListener {
            val i = Intent(this, TestimonialFilterActivity::class.java)
            i.putExtra("testimonial", testimonial)
            i.putExtra("startDate", startDate)
            i.putExtra("endDate", endDate)
            i.putExtra("productId", productId)
            i.putExtra("product", product)
            i.putExtra("rating", rating)
            i.putExtra("ratingList", ratingList)
            startActivityForResult(i, 100)
        }

        /*get data and send to Api*/
        addTestimonial.setOnClickListener {
            launchIntent(TestimonialActivity::class.java, false)
        }

        getTestimonialList()
        if (Preference.getInstance(this).getBoolean(ServiceConstrants.TESTIMONIAL_UPLOADED)) {
            val msg = if (Preference.getInstance(this)
                    .getBoolean(ServiceConstrants.UPLOADED_SUCCESS)
            ) "Testimonial Successfully Uploaded" else "Error while uploading Testimonial, Max File size should be of 50 MB."
            showdialog(msg)
        }
    }

    /*
    * method for adapter setup
    * */
    private fun setUp(list: TestimonialBean) {
        if (list.data.size == 0) {
            txtEmpty.visibility = View.VISIBLE
        } else {
            txtEmpty.visibility = View.GONE
        }
        adapter = TestimonilListAdapter(this, list.data)
        recyclerViewTestimonial.adapter = adapter
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == 100 && requestCode == 100) {
            data?.let {
                testimonial = it.getStringExtra("testimonial") ?: ""
                product = it.getStringExtra("product") ?: ""
                productId = it.getStringExtra("productId") ?: ""
                startDate = it.getStringExtra("startDate") ?: ""
                endDate = it.getStringExtra("endDate") ?: ""
                cvs = it.getStringExtra("dd") ?: ""
                rating = it.getStringExtra("rating") ?: ""
            }
            getTestimonialList()
        }
    }

    private fun getTestimonialList() {
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["testimonial_type"] = testimonial
        query["from"] = startDate
        query["to"] = endDate
        query["product"] = productId
        //query["dd_id"] = cvs
        query["rating"] = rating


        if (CheckConnectionStatus.isInternetAvailable(this)) {
            mPresenterImpl.onGetTestimonialList(query)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onStart() {
        super.onStart()

        LocalBroadcastManager.getInstance(this).registerReceiver(
            broadcastReceiver,
            IntentFilter(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
        )
    }

    override fun onPause() {
        super.onPause()
        LocalBroadcastManager.getInstance(this).unregisterReceiver(broadcastReceiver)
    }

    val broadcastReceiver: BroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            if (intent != null) {
                if (intent.hasExtra(ServiceConstrants.UPLOADING_DONE)) {
                    hideUploadingProgress()
                    showdialog("Testimonial Successfully Uploaded")
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_FAILURE)) {
                    hideUploadingProgress()
                    showdialog("Error while uploading Testimonial, Max File size should be of 50 MB.")
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_PROGRESS)) {
                    setProgressUploading(
                        intent.getLongExtra(
                            ServiceConstrants.UPLOADING_PROGRESS,
                            0
                        )
                    )
                }
                if (intent.hasExtra(ServiceConstrants.UPLOADING_PRODUCT_NAME)) {
                    setProgressUploadingName(
                        intent.getStringExtra(ServiceConstrants.UPLOADING_PRODUCT_NAME) ?: ""
                    )
                }
            }
        }
    }

    private fun setProgressUploading(progressInt: Long) {
        if (lnrUploadingDetails.visibility == View.GONE) {
            lnrUploadingDetails.visibility = View.VISIBLE
        }
        progressBar.progress = progressInt.toInt()
        txtPercentage.text = "$progressInt %"
    }

    private fun setProgressUploadingName(name: String) {
        txtFileName.text = name
    }

    private fun hideUploadingProgress() {
        if (lnrUploadingDetails.visibility == View.VISIBLE) {
            lnrUploadingDetails.visibility = View.GONE
        }
        progressBar.progress = 0
        setProgressUploadingName("")
        txtPercentage.text = "0 %"
    }

    private fun showdialog(message: String) {
        if (!isFinishing) {
            val dialog = Dialog(this)
            dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
            dialog.setContentView(R.layout.dialog_ticket_number)
            dialog.setCanceledOnTouchOutside(false)
            dialog.setCancelable(false)
            val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
            val msg = dialog.findViewById<View>(R.id.msg) as Medium
            val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
            ticketNumber.visibility = View.GONE
            tic_banner.visibility = View.GONE
            msg.text = message
            val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
            tvOkay.setOnClickListener {
                dialog.dismiss()
                getTestimonialList()
                Preference.getInstance(this)
                    .putBolean(ServiceConstrants.TESTIMONIAL_UPLOADED, false)
            }
            dialog.show()
        }
    }
}
