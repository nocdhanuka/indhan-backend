package com.dhanuka.indhan.DemoRequestActivity.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestOfflineModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_demo_request.view.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class OfflineDemoRequestAdapter(context: Context, demoList: ArrayList<DemoRequestOfflineModel>) :
    RecyclerView.Adapter<OfflineDemoRequestAdapter.ViewHolder>() {
    var context: Context
    var demoList: ArrayList<DemoRequestOfflineModel>

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
        holder.view.txtDemoNo.text = "Demo no: "
        holder.view.txtCropName.text = demoList.get(position).cropName
        holder.view.productName.text = demoList.get(position).productName
        holder.view.txtFarmerName.text = demoList.get(position).farmerName

        holder.view.txtCreatedDate.text = demoList[position].showingDate!!
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