class UserState{
    user = null;
    setUser(user){
        this.user = user;
    }

    getUser(){
        return this.user;
    }
}

let userState = new UserState();
export default userState;
