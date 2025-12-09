package com.dhanuka.indhan.institutional.activity.influencers

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.R
import com.dhanuka.indhan.TestimonialList.filter.TestimonialFilterActivity
import com.dhanuka.indhan.VideoPlayer.VideoPlayActivity
import com.dhanuka.indhan.institutional.adapter.influencers.InfluencersTestimonialListAdapter
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import kotlinx.android.synthetic.main.activity_testimonial_list.*

class InfluencersTestimonialListActivity : BaseActivity(),
    InfluencersTestimonialListAdapter.OnClickItem {
    private var listOfData: ArrayList<InfluencerTestimonialData> = ArrayList()
    private var adapter: InfluencersTestimonialListAdapter? = null
    private var testimonial = ""
    private var product = ""
    private var productId = ""
    private var startDate = ""
    private var endDate = ""
    private var cvs = ""
    private var rating = ""
    private var ratingList = ""
    override fun onClick(v: View?) {
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_testimonial_list)

        imgFilter.visibility = View.GONE
        addTestimonial.visibility = View.GONE
        listOfData = Gson().fromJson(
            intent?.getStringExtra("data")!!,
            object : TypeToken<ArrayList<InfluencerTestimonialData?>?>() {}.type
        ) as ArrayList<InfluencerTestimonialData>

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
        setAdapter()
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
            filterTestimonial()
        }
    }

    private fun filterTestimonial() {

    }


    private fun setAdapter() {
        adapter = InfluencersTestimonialListAdapter(this, listOfData)
        recyclerViewTestimonial.adapter = adapter
        adapter?.setClickListener(this)
    }

    override fun onClickView(view: View, subMenu: InfluencerTestimonialData) {
        val intent = Intent(this@InfluencersTestimonialListActivity, VideoPlayActivity::class.java)
        intent.putExtra("VideoUrl", Constants.IMAGEBASEURL.plus(subMenu.uploaded_file))
        startActivity(intent)
    }

    override fun onClickViewCircular(view: View, subMenu: InfluencerTestimonialData) {
        val intent = Intent()
        intent.putExtra("position", this.intent?.getIntExtra("position", 0))
        intent.putExtra("data", Gson().toJson(subMenu))
        setResult(RESULT_OK, intent)
        finish()
    }


}
