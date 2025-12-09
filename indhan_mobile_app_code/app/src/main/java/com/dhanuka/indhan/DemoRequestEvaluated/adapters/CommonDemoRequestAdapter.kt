package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import android.content.Intent
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.activities.RequestEvaluatedDetailsActivity
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedlist

import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_demo_request_evaluated.view.*
import java.io.Serializable
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*


class CommonDemoRequestAdapter(context: Context, ddList: ArrayList<DemoRequestEvaluatedlist>) :
    RecyclerView.Adapter<CommonDemoRequestAdapter.ViewHolder>() {
    var context: Context
    var ddList: ArrayList<DemoRequestEvaluatedlist>

    init {
        this.context = context
        this.ddList = ddList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View = LayoutInflater.from(context)
            .inflate(R.layout.item_demo_request_evaluated, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtDdname.text = ddList.get(position).dd_name
        holder.view.txtFarmerName.text = ddList.get(position).farmer_name
        holder.view.txtCropName.text = ddList.get(position).crop
        var suggestedDate = getTime(ddList.get(position).updated_at)
        holder.view.txtSuggestedDate.text = suggestedDate
        holder.view.txtProduct.text = ddList.get(position).product
        holder.view.txtCreatedOn.text = "Created on : " + getTime(ddList.get(position).created_at)
        holder.view.txtDemoNo.text = "Demo no:" + ddList.get(position).demo_code
        holder.view.llDemoDetails.setOnClickListener {
            var intent = Intent(context, RequestEvaluatedDetailsActivity::class.java)
            intent.putExtra(Constants.SHOW_DETAILS, ddList.get(position) as Serializable)
            context.startActivity(intent)

        }
        //set image
        holder.view.rvFeedbackImage.layoutManager =
            LinearLayoutManager(
                context,
                LinearLayoutManager.HORIZONTAL,
                false
            )
        var feedbackAdapter: FeedbackImageAdapter = FeedbackImageAdapter(
            context,
            ddList.get(position).demo_feedback,
            ddList.get(position).demoImage
        )
        holder.view.rvFeedbackImage.adapter = feedbackAdapter
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
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
}