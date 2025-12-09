package com.dhanuka.indhan.TestimonialList.supervisor

import android.content.Intent
import android.os.Bundle
import android.os.Parcelable
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.TestimonialFilterActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.validatefarmers.SuccessBean
import kotlinx.android.synthetic.main.activity_testimonial_supervisor.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class TestimonialSupervisorActivity : BaseActivity(), SwipeRefreshLayout.OnRefreshListener {
    override fun onRefresh() {
        swipeRefreshLayout.setRefreshing(false)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getTestimonialListAPI()
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(v: View) {


    }

    private lateinit var preference: Preference

    private lateinit var adapter: TestimonialAdapter

    private var testimonial = ""
    private var product = ""
    private var productId = ""
    private var startDate = ""
    private var endDate = ""
    private var cvs = ""

    private var rsm = ""
    private var asm = ""
    private var fl = ""

    var flName = ""
    var asmName = ""
    var rsmName = ""

    var parce: Parcelable? = null

    private var rating = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_testimonial_supervisor)
        preference = Preference(this)
        swipeRefreshLayout.setOnRefreshListener(this)

        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getTestimonialListAPI()
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
        pd_name.text = "Master Testimonials"
        imgFilter.setOnClickListener {
            val i = Intent(this, TestimonialFilterActivity::class.java)
            i.putExtra("testimonial", testimonial)
            i.putExtra("startDate", startDate)
            i.putExtra("endDate", endDate)
            i.putExtra("product", product)
            i.putExtra("productId", productId)
            i.putExtra("rating", rating)

            i.putExtra("dd", cvs)

            i.putExtra("rsm", rsmName)
            i.putExtra("asm", asmName)
            i.putExtra("fl", flName)

            startActivityForResult(i, 100)

        }

    }

    private fun getTestimonialListAPI() {
        showProgress()
        val query = HashMap<String, Any>()

        //rating (comma separated), myself (1,0), rsm, asm, bdo_id,  dd_id, from (date format: YYYY-mm-dd), to (date format: YYYY-mm-dd), product, crop
        query["apiToken"] = preference.getString("token")
        query["rsm"] = rsm
        query["asm"] = asm
        query["bdo_id"] = fl
        query["from"] = startDate
        query["to"] = endDate
        query["testimonial_type"] = testimonial
        query["product"] = productId
        query["dd_id"] = cvs
        query["rating"] = rating
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).supervisorTestimonialList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SupervisorTestimonialBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: SupervisorTestimonialBean) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        txtTotalACP.text = villageBean.data.totalTestimonials.toString()
                        txtTotalAcrerageValue.text =
                            villageBean.data.totalBdFlTestimonials.toString()
                        txtTotalLeadValue.text = villageBean.data.totalSFlTestimonials.toString()
                        val userId = preference.getString("user_id")
                        if (villageBean.data.testimonialData.size == 0) {
                            txtEmpty.visibility = View.VISIBLE
                        } else {
                            txtEmpty.visibility = View.GONE
                        }
                        adapter =
                            com.dhanuka.indhan.TestimonialList.supervisor.TestimonialAdapter(this@TestimonialSupervisorActivity,
                                preference.getString("ecn"),
                                preference.getString("user_type"),
                                villageBean.data.testimonialData, object : TestimonialListener {
                                    override fun onRating(id: String, rating: String) {
                                        val query = HashMap<String, Any>()
                                        query["apiToken"] = preference.getString("token")
                                        query["testimonial_id"] = id
                                        query["rating"] = rating
                                        if (CheckConnectionStatus.isInternetAvailable(this@TestimonialSupervisorActivity)) {
                                            rateTestimonialsAPI(query)
                                        } else {
                                            makeToast(resources.getString(R.string.no_internet_connection))
                                        }

                                    }
                                })
                        recyclerViewTestimonial.layoutManager =
                            LinearLayoutManager(this@TestimonialSupervisorActivity)
                        recyclerViewTestimonial.adapter = adapter
                        if (parce != null)
                            recyclerViewTestimonial.getLayoutManager()
                                ?.onRestoreInstanceState(parce);//restore
                    } else {
                        if (villageBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        }
                    }
                }
            }
            )
    }

    private fun rateTestimonialsAPI(model: HashMap<String, Any>) {
        showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).testimonialRating(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: SuccessBean) {
                    if (CheckConnectionStatus.isInternetAvailable(activity)) {
                        getTestimonialListAPI()
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                    parce = recyclerViewTestimonial.getLayoutManager()?.onSaveInstanceState();//save
                    makeToast(villageBean.message)
                }
            })
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 100) {
            data?.let {
                testimonial = it.getStringExtra("testimonial") ?: ""
                product = it.getStringExtra("product") ?: ""
                productId = it.getStringExtra("productId") ?: ""
                startDate = it.getStringExtra("startDate") ?: ""
                endDate = it.getStringExtra("endDate") ?: ""
                cvs = it.getStringExtra("dd") ?: ""
                rsm = it.getStringExtra("rsm") ?: ""
                asm = it.getStringExtra("asm") ?: ""
                fl = it.getStringExtra("fl") ?: ""
                rating = it.getStringExtra("rating") ?: ""
                flName = it.getStringExtra("flName") ?: ""
                asmName = it.getStringExtra("asmName") ?: ""
                rsmName = it.getStringExtra("rsmName") ?: ""
            }
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                getTestimonialListAPI()
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }

        }
    }
}
