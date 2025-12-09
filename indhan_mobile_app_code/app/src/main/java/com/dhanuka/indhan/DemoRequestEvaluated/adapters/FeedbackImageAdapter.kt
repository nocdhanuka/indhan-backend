package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluateFeedback
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_image.view.*


class FeedbackImageAdapter(
    context: Context,
    ddList: List<DemoRequestEvaluateFeedback>,
    demoImage: String
) : RecyclerView.Adapter<FeedbackImageAdapter.ViewHolder>() {
    var index = -1
    var context: Context
    var ddList: List<DemoRequestEvaluateFeedback>
    var demoImage: String = ""

    init {
        this.context = context
        this.ddList = ddList
        this.demoImage = demoImage
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View = LayoutInflater.from(context).inflate(R.layout.item_image, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        if (!demoImage.isNullOrBlank())
            return ddList.size + 1
        else
            return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        if (!demoImage.isNullOrBlank()) {
            if (position == 0) {
                if (!demoImage.isNullOrBlank()) {
                    Glide.with(context)
                        .load(demoImage)
                        .into(holder.view.imgFeedback)
                    holder.view.txtName.text = "Uploaded by DD"
                }
            } else {
                index = position - 1
                if (!ddList.get(index).feedbackImage.isNullOrBlank()) {
                    Glide.with(context)
                        .load(ddList.get(index).feedbackImage)
                        .into(holder.view.imgFeedback)
                }
                holder.view.txtName.text = ddList.get(index).status
            }

        } else {
            if (!ddList.get(position).feedbackImage.isNullOrBlank()) {
                Glide.with(context)
                    .load(ddList.get(position).feedbackImage)
                    .into(holder.view.imgFeedback)
            }
            holder.view.txtName.text = ddList.get(position).status
        }

    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }

}