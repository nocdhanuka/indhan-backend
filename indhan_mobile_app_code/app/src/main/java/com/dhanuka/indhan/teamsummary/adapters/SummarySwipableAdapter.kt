package com.dhanuka.indhan.teamsummary.adapters

import android.content.Context
import android.os.Build
import androidx.annotation.RequiresApi
import androidx.viewpager.widget.PagerAdapter
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.LinearLayout
import com.dhanuka.indhan.R
import com.dhanuka.indhan.teamsummary.models.TeamSummaryUpcomingATP
import kotlinx.android.synthetic.main.item_upcoming_atp.view.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

class SummarySwipableAdapter(context: Context, list: ArrayList<TeamSummaryUpcomingATP>) :
    PagerAdapter() {
    var context: Context
    var list: ArrayList<TeamSummaryUpcomingATP>
    var inflater: LayoutInflater
    override fun isViewFromObject(p0: View, p1: Any): Boolean {
        return p0 === (p1 as LinearLayout)
    }

    init {
        this.context = context
        this.list = list
        inflater = LayoutInflater.from(context)
    }

    @RequiresApi(Build.VERSION_CODES.O)
    override fun instantiateItem(container: ViewGroup, position: Int): Any {
        val view = inflater.inflate(R.layout.item_upcoming_atp, container, false)//item_event
        val viewHolder = MyViewHolder(view)
        if (!list.get(position).plan_date.isNullOrBlank()) {
            viewHolder.itemView.txtUpcommingDate.text = getTime(list.get(position).plan_date)

        }
        viewHolder.itemView.txtDdName.text = list.get(position).name
        container.addView(view)
        return view
    }


    override fun getCount(): Int {
        return list.size
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view)

    override fun destroyItem(container: ViewGroup, position: Int, `object`: Any) {
        container.removeView(`object` as LinearLayout)
    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS")
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