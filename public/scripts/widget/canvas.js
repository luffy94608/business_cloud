(function ($) {

    function Ball(x,y,radius,color) {
        this.scale = 0.5;
        this.x = x ? x : 0;
        this.y =  y ? y : 0;
        this.radius =  radius ? radius*this.scale : 100 * this.scale;
        this.color =  utils.parseColor(color);
        this.vx = 0;
        this.vy = 0;
        this.mass = 1;
        this.scaleX = 1;
        this.scaleY = 1;
    }

    Ball.prototype.draw = function (ctx) {
        ctx.save();
        ctx.translate(this.x,this.y);
        ctx.scale(this.scaleX/this.scale,this.scaleY/this.scale);
        ctx.imageSmoothingEnabled = true;
        ctx.fillStyle = this.color;
        // ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(0,0,this.radius,0,Math.PI*2,false);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    };

    Ball.prototype.getBounds = function () {
        return {
            x:this.x-this.radius,
            y:this.y-this.radius,
            width:this.radius*2,
            height:this.radius*2
        }
    };
        
    window.utils = {};
//动画循环
    if (!window.requestAnimationFrame) {
        window.requestAnimationFrame = (window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        function (callback) {
            return window.setTimeout(callback, 17 /*~ 1000/60*/);
        });
    }

//动画循环取消
    if (!window.cancelAnimationFrame) {
        window.cancelAnimationFrame = (window.cancelRequestAnimationFrame ||
        window.webkitCancelAnimationFrame || window.webkitCancelRequestAnimationFrame ||
        window.mozCancelAnimationFrame || window.mozCancelRequestAnimationFrame ||
        window.msCancelAnimationFrame || window.msCancelRequestAnimationFrame ||
        window.oCancelAnimationFrame || window.oCancelRequestAnimationFrame ||
        window.clearTimeout);
    }

    utils.parseColor = function (color, toNumber) {
        if (toNumber === true) {
            if (typeof color === 'number') {
                return (color | 0); //chop off decimal
            }
            if (typeof color === 'string' && color[0] === '#') {
                color = color.slice(1);
            }
            return window.parseInt(color, 16);
        } else {
            if (typeof color === 'number') {
                color = '#' + ('00000' + (color | 0).toString(16)).substr(-6); //pad
            }
            return color;
        }
    };

    utils.containsPoint = function (rect,x,y) {
        return !(x<rect.x || y<rect.y || x>rect.x+rect.width || y>rect.y+rect.height)
    };

    /**
     * 碰撞检测
     */
    utils.intersects = function (ballA,ballB) {
        return !(ballA.x>ballB.x+ballB.width || ballA.x+ballA.width<ballB.x || ballA.y>ballB.y+ballB.height || ballA.y+ballA.height<ballB.y);
    };

    /**
     * 碰撞检测 球体
     * @param ballA
     * @param ballB
     * @returns {boolean}
     */
    utils.intersectBalls = function (ballA,ballB) {
        var len = ballA.radius+ballB.radius;
        var distance = Math.floor(Math.sqrt((Math.pow(ballA.x-ballB.x,2)+Math.pow(ballA.y-ballB.y,2))));
        return (len>distance);
    };

    utils.randomInt = function (min,max) {
        return (min+Math.random()*(max-min));
    };

    utils.random = function (min,max) {
        return Math.floor(min+Math.random()*(max-min));
    };


    utils.calcDistance = function (mouse,ball,distance) {
        var len = distance ? distance : 100;
        var space = Math.floor(Math.sqrt((Math.pow(mouse.x-ball.x,2)+Math.pow(mouse.y-ball.y,2))));
        return (len>space);
    };


    /**
     * 坐标旋转
     */
    utils.rotate = function (x, y, sin, cos , reverse) {
        return {
            x: reverse ? (x*cos - y*sin) : ( x*cos + y*sin),
            y: reverse ? (y*cos + x*sin) : ( y*cos - x*sin)
        };
    };

    /**
     * 墙壁检测
     */
    utils.checkWallCollision = function (ball,bonus,w,h) {
        if(ball.x + ball.radius >= w){
            ball.vx *=bonus;
            ball.x = w - ball.radius;
        }
        if(ball.x - ball.radius <=0){
            ball.vx *= bonus;
            ball.x = ball.radius;
        }
        if(ball.y + ball.radius >= h){
            ball.vy *= bonus;
            ball.y = h - ball.radius;
        }
        if(ball.y - ball.radius <= 0){
            ball.vy *= bonus;
            ball.y = ball.radius;
        }
    };


    /**
     * 物体碰撞
     */

    utils.checkBallCollision = function (ballA,ballB) {
        if(utils.intersectBalls(ballA, ballB)){
            //以 A 为圆点 计算旋转弧度
            var dx = ballB.x - ballA.x;
            var dy = ballB.y - ballA.y;
            var rotation = Math.atan2(dy,dx);
            var sin = Math.sin(rotation);
            var cos = Math.cos(rotation);

            //旋转坐标 速度
            var posA = {x:0,y:0};
            var posB = utils.rotate(dx,dy,sin,cos,true);
            var verA = utils.rotate(ballA.vx,ballA.vy,sin,cos,true);
            var verB = utils.rotate(ballB.vx,ballB.vy,sin,cos,true);

            //碰撞计算 旋转后为水平面 不考虑y轴速度
            //TODO warning
            var vxTotal =  verA.x - verB.x;//总速度
//                    var vxTotal =  verB.x - verA.x;//总速度
            verA.x = ((ballA.mass - ballB.mass)*verA.x + 2*ballB.mass*verB.x) /(ballA.mass + ballB.mass);
            verB.x = vxTotal + verA.x;

            //重叠部分处理
            var absVx = Math.abs(verA.x) + Math.abs(verB.x);//合速度
            var overLen = ballA.radius + ballB.radius;
            var overlap = overLen - Math.abs(ballB.x - ballA.x);//重叠距离 或者 相离距离
            var t = (overlap / absVx);
            t = 1;
            //碰撞后反弹距离
            posA.x += verA.x * t;//碰撞时间 * 速度 反弹距离
            posB.x += verB.x * t;//碰撞时间 * 速度 反弹距离

            //旋转到开始位置
            var posFa = utils.rotate(posA.x,posA.y,sin,cos,false);
            var posFb = utils.rotate(posB.x,posB.y,sin,cos,false);

            var verFa = utils.rotate(verA.x,verA.y,sin,cos,false);
            var verFb = utils.rotate(verB.x,verB.y,sin,cos,false);

            //旋转位置和速度计算
            //坐标
            ballA.x = ballA.x + posFa.x;
            ballA.y = ballA.y + posFa.y;
            ballB.x = ballA.x + posFb.x;
            ballB.y = ballA.y + posFb.y;

            //速度
            ballA.vx = verFa.x;
            ballA.vy = verFa.y;
            ballB.vx = verFb.x;
            ballB.vy = verFb.y;

        }
    };


    $.resizeCanvasWindow = function(canvas) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };
    $.canvasAntCollision = function (id, opts) {
        var st = {
            maxDistance:20,
            maxSpeed:0.2,
            radius:4,
            bonus:-1,
            isRandRadius: true,
            isRandColor: false,
            hasLine: true,
            ballLen: 70,
            ballArr: [],
            strokeStyle : 'rgba(255, 255, 255, 0.1)'
        };
        if (typeof opts === 'object') {
            $.extend(st, opts);
        }
        var canvas = document.getElementById(id);
        var ctx = canvas.getContext('2d');
        var w = canvas.width,h = canvas.height;

        for (var i =0 ;i<st.ballLen;i++){
            var bRadius = utils.randomInt(1, st.radius);
            if (!st.isRandRadius) {
                bRadius = 3;
            }
            var color = 'rgba('+utils.random(0,255)+','+utils.random(0,255)+','+utils.random(0,255)+',1)';
            if (!st.isRandColor) {
                 color = ' #fff';
            }
            var ball = new Ball(utils.randomInt(st.radius,w-st.radius),utils.randomInt(st.radius,h-st.radius),bRadius, color);
            ball.vx = utils.randomInt(-st.maxSpeed,st.maxSpeed);
            ball.vy = utils.randomInt(-st.maxSpeed,st.maxSpeed);
            ball.mass =1;
            ball.scale = 1;
            st.ballArr.push(ball);
        }
        function drawFrame() {
            ctx.clearRect(0,0,w,h);
            for (var i =0 ;i<st.ballLen;i++){
                for (var j=i+1;j<st.ballLen;j++){
                    utils.checkBallCollision(st.ballArr[i],st.ballArr[j]);
                    if (st.hasLine) {
                        drawLine(st.ballArr[i],st.ballArr[j], st.maxDistance);
                    }
                    utils.checkWallCollision(st.ballArr[i], st.bonus, w, h);
                    utils.checkWallCollision(st.ballArr[j], st.bonus, w, h);
                }

                st.ballArr[i].x += st.ballArr[i].vx;
                st.ballArr[i].y += st.ballArr[i].vy;
                st.ballArr[i].draw(ctx);
            }
            window.requestAnimationFrame(drawFrame);
        }
        window.requestAnimationFrame(drawFrame);

        /**
         * 连线
         */
        function drawLine(ballA,ballB, maxDistance) {
            var distance = Math.sqrt(Math.pow(ballB.y-ballA.y,2)+Math.pow(ballB.x-ballA.x,2));
            if(distance<=maxDistance){
                ctx.save();
                ctx.strokeStyle = st.strokeStyle;
                ctx.beginPath();
                ctx.moveTo(ballA.x,ballA.y);
                ctx.lineTo(ballB.x,ballB.y);
                ctx.closePath();
                ctx.stroke();
                ctx.restore();
            }
        }
    }

})($);


