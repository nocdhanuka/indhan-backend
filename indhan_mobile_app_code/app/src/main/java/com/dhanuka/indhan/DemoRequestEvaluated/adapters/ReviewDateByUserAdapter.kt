package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluateFeedback
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_review_by_user.view.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*


class ReviewDateByUserAdapter(
    context: Context,
    userFeedBackList: ArrayList<DemoRequestEvaluateFeedback>
) : RecyclerView.Adapter<ReviewDateByUserAdapter.ViewHolder>() {
    var context: Context
    var userFeedBackList: ArrayList<DemoRequestEvaluateFeedback>

    init {
        this.context = context
        this.userFeedBackList = userFeedBackList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_review_by_user, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return userFeedBackList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtReviewDate.text = getTime(userFeedBackList.get(position).creation_at)
        holder.view.txtEvaluationStatus.text = userFeedBackList.get(position).status
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