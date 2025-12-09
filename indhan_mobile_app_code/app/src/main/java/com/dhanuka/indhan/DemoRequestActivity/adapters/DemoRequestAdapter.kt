package com.dhanuka.indhan.DemoRequestActivity.adapters

import android.content.Context
import android.content.Intent
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.activity.RequestDetailsActivity.RequestsDetailActivity
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestData
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_demo_request.view.*
import java.io.Serializable
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class DemoRequestAdapter(context: Context, demoList: ArrayList<DemoRequestData>) :
    RecyclerView.Adapter<DemoRequestAdapter.ViewHolder>() {
    var context: Context
    var demoList: ArrayList<DemoRequestData>

    init {
        this.context = context
        this.demoList = demoList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_demo_request, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return demoList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtDemoNo.text = "Demo no: " + demoList.get(position).demo_code
        holder.view.txtCropName.text = demoList.get(position).crop
        holder.view.productName.text = demoList.get(position).product
        holder.view.txtFarmerName.text = demoList.get(position).farmer_name

        holder.view.txtCreatedDate.text = getTime(demoList[position].created_at)
        holder.view.llProductDetail.setOnClickListener {
            var showDetailIntent = Intent(context, RequestsDetailActivity::class.java)
            showDetailIntent.putExtra(
                Constants.SEND_DEMO_REQUEST_DATA,
                demoList.get(position) as Serializable
            )
            context.startActivity(showDetailIntent)
        }

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